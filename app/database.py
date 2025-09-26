from sqlalchemy import create_engine
import pandas as pd

engine = create_engine("mysql+pymysql://root:@localhost/devices")

def get_inventaris_produk():
    query = "SELECT id, type_produk, produk, no_sn, nama_client, garansi_awal FROM inventaris_produk;"
    with engine.connect() as con:
        return pd.read_sql(query, con=con)
    

def get_detail_maintenance():
    query = "SELECT detail_id, transaksi_id, produk_mt, no_sn, garansi FROM detail_maintenance;"
    with engine.connect() as con:
        return pd.read_sql(query, con=con)

def get_transaksi_maintenance():
    query = "SELECT transaksi_id, tanggal_terima, nama_client FROM transaksi_maintenance;"
    with engine.connect() as con:
        return pd.read_sql(query, con=con)

def get_historis_maintenance():
    query = "SELECT nama_barang, quantity, deskripsi FROM historis WHERE deskripsi LIKE '%%maintenance%%'"
    with engine.connect() as con:
        return pd.read_sql(query, con=con)
