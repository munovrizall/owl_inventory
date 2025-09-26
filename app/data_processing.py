
from database import *

import pandas as pd

def standardize_nama_clint(df):
    df['nama_client'] = df['nama_client'].str.replace(r'^([^-]+)-.*$', r'\1', regex=True)
    return df

# Produksi
def get_produksi(): 
    df = get_inventaris_produk()
    df.dropna(inplace=True)

    df['no_sn'] = df['no_sn'].astype(int)
    df = df[df['no_sn'].dropna().astype(str).str.len() == 8] # Filter data dengan sn yang valid

    df = standardize_nama_clint(df) # Standarisasi nama client

    cat_cols = ['type_produk','produk', 'nama_client']
    for col in cat_cols:
        df[col] = df[col].astype('category') # Ubah tipe kolom kategori

    type_orders = df.type_produk.unique().tolist()
    df['type_produk'] = df['type_produk'].cat.set_categories(type_orders, ordered=True)

    product_orders = df.groupby(['type_produk', 'produk'], observed=True).size().index.get_level_values(1).tolist()
    df['produk'] = df['produk'].cat.set_categories(product_orders, ordered=True)

    df['julian_date'] = (df['no_sn']/1000).astype(int)
    df['waktu'] = pd.to_datetime(
        df['julian_date'].apply(lambda x: f"{2000 + x // 1000}-{x % 1000}"), 
        format='%Y-%j', errors='coerce'
    )

    return df

# Maintenance
def get_maintenance():
    df1 = get_detail_maintenance()
    df2 = get_transaksi_maintenance()
    df3 = get_inventaris_produk()

    df = df1.merge(df2, on='transaksi_id', how='inner')
    df = df.merge(df3[['no_sn', 'garansi_awal', 'type_produk']], on='no_sn', how='left')

    df.rename(columns={'tanggal_terima': 'waktu'}, inplace=True)
    df['garansi'] = pd.to_numeric(df['garansi'], errors='coerce').fillna(0).astype(bool)
    df = standardize_nama_clint(df) # Standarisasi nama client

    cat_cols = ['produk_mt','type_produk', 'nama_client']
    for col in cat_cols:
        df[col] = df[col].astype('category')

    type_orders = df['type_produk'].cat.categories.tolist()
    type_orders = [cat for cat in get_produksi()['type_produk'].unique().tolist() if cat in type_orders]
    df['type_produk'] = df['type_produk'].cat.set_categories(type_orders, ordered = True)

    product_orders = df.groupby(['type_produk', 'produk_mt'], observed=True).size().index.get_level_values(1).tolist()
    df['produk_mt'] = df['produk_mt'].cat.set_categories(product_orders, ordered=True)

    dt_cols = ['waktu', 'garansi_awal']
    for col in dt_cols:
        df[col] = pd.to_datetime(df[col], errors='coerce')

    df['selisih_hari'] = (df['waktu'] - df['garansi_awal']).dt.days
    df['selisih_bulan'] = (df['selisih_hari'] / 30)

    return df

def get_komponen():
    df = get_historis_maintenance()

    pattern = r'^Maintenance/.+/([^/]+)/'
    df['detail_id'] = df['deskripsi'].str.extract(pattern, expand=False).astype(int)
    df[['kelompok_barang', 'barang']] = df['nama_barang'].str.split(' - ', expand=True)

    df.drop(columns=["nama_barang", "deskripsi"], inplace=True)

    return df
