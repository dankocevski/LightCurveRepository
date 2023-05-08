import json
import os
import urllib

import pandas as pd

def load_dataframe(source, cadence='weekly', flux_type='photon',
          index_type='fixed', ts_min=4):

  url_template = (
    "https://fermi.gsfc.nasa.gov/ssc/data/access/lat/LightCurveRepository/"
    "queryDB.php?typeOfRequest=lightCurveData"
    "&source_name={source_name}&cadence={cadence}&flux_type={flux_type}"
    "&index_type={index_type}&ts_min={ts_min}&magicWord=130427A"
    )

  quoted_source = urllib.parse.quote(source)
  filename = '_'.join([quoted_source, cadence, flux_type,
            index_type, "tsmin" + str(ts_min)])
  filename += ".json"
  if not os.path.exists(filename):
    print("Downloading data...")
    url = url_template.format(**{"source_name": quoted_source,
                   "cadence": cadence,
                   "flux_type": flux_type,
                   "index_type": index_type,
                   "ts_min": ts_min})
    with urllib.request.urlopen(url) as response:
      data = json.loads(response.read().decode())
    with open(filename, 'w') as f:
      json.dump(data, f)
  raw_df = pd.read_json(filename, orient='index').T
  raw_df = raw_df.rename(columns={'fit_convergance': 'fit_convergence'})

  df = pd.DataFrame(raw_df[raw_df.columns[-4:]])
  df['time_MET'] = [ts[0] for ts in raw_df['ts']]
  MJDREF = 51910 + 7.428703703703703e-4
  df['time_MJD'] = MJDREF + df['time_MET']/86400
  df = df.set_index('time_MET')

  def split_list(v):
    if len(v) == 2:
      return v
    else:
      return [v[0], (v[2] - v[1])/2]

  def join_col(col):
    join_df = pd.DataFrame([split_list(v) for v in raw_df[col]
                if v is not None])
    join_df = join_df.rename(columns={0: 'time_MET', 1: col})\
             .set_index('time_MET')
    return df.join(join_df)

  for col in raw_df.columns[:-4]:
    df = join_col(col)
  df = df.reset_index().set_index('bin_id').sort_index(axis=1)
  return df

