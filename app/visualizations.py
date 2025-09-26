from data_processing import *

import pandas as pd
import numpy as np

import plotly
import plotly.express as px
import plotly.graph_objects as go
from plotly.colors import qualitative, sequential

import json

# Produksi per Bulan
def monthly_production(df):

    if not (df['waktu'].dt.to_period('M') == pd.Timestamp.now().to_period('M')).any():    
        curent_time = pd.DataFrame([{'waktu': pd.Timestamp.now()}])
        df = pd.concat([df, curent_time], ignore_index=True)

    data = df.resample('ME', on='waktu').produk.value_counts().reset_index(name='qty').rename(columns={'waktu': 'periode'})

    data['periode'] = data['periode'].dt.to_period('M').astype(str)
    data['qty'] = data['qty'].replace(0, None)
    
    total = data.groupby('periode').qty.sum()#.replace(0, None)

    fig = px.bar(
        data,
        x='periode',
        y='qty',
        color='produk',
        color_discrete_sequence=qualitative.Pastel,
        title="Produksi Per Bulan",
        labels={'qty': 'Jumlah Produk', 'periode': 'Periode'},
        barmode='stack',

    )

    fig.add_trace(
        go.Scatter(
            x=total.index,
            y=total.values,
            mode='lines',
            line=dict(color='black'),
            opacity=0,
            name='Total',
            showlegend=False
        )
    )
    
    # Update layout for better visualization
    fig.update_layout(
        xaxis=dict(
            tickformat="%b\n%Y",
            title="Peroide",
            rangemode="normal",
            rangeselector=dict(
                buttons=[
                    dict(count=6, label='6M', step='month', stepmode='backward'),
                    dict(count=1, label='1Y', step='year', stepmode='backward'),
                    dict(count=1, label='YTD', step='year', stepmode='todate'),
                    dict(step='all', label='All')
                ]
            ),
            rangeslider=dict(
                visible=True,
            ),
            type="date",
            showspikes=False,
            fixedrange=True,
            linecolor = 'black'
        ),
        yaxis=dict(
            title="Jumlah Produksi",
            gridcolor = 'lightgrey',
            gridwidth = 1,
            rangemode="tozero"
        ),
        title=dict(
            x=0.5,
        ),
        legend_title=None,
        bargap=0.2,
        plot_bgcolor="rgba(0,0,0,0)",
        hovermode = 'x unified'
    )

    fig.update_traces(
        hovertemplate='%{y}',
        marker=dict(
            line=dict(width=0)
        )
    )

    config = dict(
        responsive=True,
        displaylogo=False,
        modeBarButtons= [['toImage']],
    )

    return json.dumps(dict(data = fig.data,
                           layout = fig.layout,
                           config = config
                           ), 
                           cls= plotly.utils.PlotlyJSONEncoder)

# Distribusi tpe produk
def product_type_distribution(df):

    data = df.type_produk.value_counts()

    fig = px.pie(data, 
             values=data.values, 
             names=data.index, 
             title="Distribusi Tipe Produk",
             color_discrete_sequence=qualitative.T10, 
             )

    fig.update_layout(
        paper_bgcolor='rgba(0,0,0,0)',
        title=dict(
            x=0.5,
        ),
        font=dict(
            # color='black',
        ),
    )

    fig.update_traces(
        hovertemplate='%{label}: %{value})',
        textinfo='percent+label',
        # textposition='inside',
        # insidetextorientation='radial'
    )

    config = dict(
        responsive=True,
        displaylogo=False,
        modeBarButtons= [['toImage']],
    )

    return json.dumps(dict(data = fig.data,
                           layout = fig.layout,
                           config = config
                           ), 
                           cls= plotly.utils.PlotlyJSONEncoder)

# Distribusi produk
def product_distribution(df):

    data = df[['type_produk', 'produk']].value_counts()

    fig = px.bar(
        data, 
        y=data.index.get_level_values(1), 
        x=data.values, 
        color=data.index.get_level_values(0),
        color_discrete_sequence=px.colors.qualitative.T10,
        title="Distribusi Produk",
        # category_orders={'produk': pr_qty.sort_values(by='qty', ascending=False)['produk'].tolist()}
    )

    fig.update_layout(
        yaxis=dict(
            categoryorder='total descending',
            autorange="reversed",
            title=None,
            fixedrange=True,
        ),
        xaxis=dict(
            title=None,
            fixedrange=True,
        ),
        title_x=0.5,  
        plot_bgcolor="rgba(0,0,0,0)",
        legend_title=None,
        # paper_bgcolor="rgba(0,0,0,0)",
    )

    fig.update_traces(
        hovertemplate='%{x}',
        
    )

    config = dict(
        displaylogo=False,
        modeBarButtons= [['toImage']],
    )

    return json.dumps(dict(data = fig.data,
                           layout = fig.layout,
                           config = config
                           ), 
                           cls= plotly.utils.PlotlyJSONEncoder)

# Maintenance per Bulan
def monthly_maintenance(df):
    if not (df['waktu'].dt.to_period('M') == pd.Timestamp.now().to_period('M')).any():    
        curent_time = pd.DataFrame([{'waktu': pd.Timestamp.now()}])
        df = pd.concat([df, curent_time], ignore_index=True)

    data = df.resample('ME', on='waktu').produk_mt.value_counts().reset_index(name='qty').rename(columns={'waktu': 'periode'})

    data['periode'] = data['periode'].dt.to_period('M').astype(str)
    data['qty'] = data['qty'].replace(0, None)

    total = data.groupby('periode').qty.sum()#.replace(0, None)

    fig = px.bar(
        data,
        x='periode',
        y='qty',
        color='produk_mt',
        color_discrete_sequence=qualitative.Pastel,
        title="Maintenance Per Bulan",
        labels={'qty': 'Jumlah Produk', 'periode': 'Periode'},
        barmode='stack',

    )

    fig.add_trace(
        go.Scatter(
            x=total.index,
            y=total.values,
            mode='lines',
            line=dict(color='black'),
            opacity=0,
            name='Total',
            showlegend=False
        )
    )

    fig.update_layout(
        xaxis=dict(
            tickformat="%b\n%Y",
            title="Bulan",
            rangeselector=dict(
                buttons=[
                    dict(count=6, label='6m', step='month', stepmode='backward'),
                    dict(count=1, label='1y', step='year', stepmode='backward'),
                    dict(count=1, label='This Year', step='year', stepmode='todate'),
                    dict(step='all')
                ]
            ),
            rangeslider=dict(visible=True),
            type="date",
            showspikes=False,
            fixedrange=True,
            linecolor = 'black'
        ),
        yaxis=dict(
            title="Jumlah Produksi",
            gridcolor = 'lightgrey',
            gridwidth = 1,
            rangemode="tozero"

        ),
        title_x=0.5,
        # legend_title="Produk",
        bargap=0.2,  # Adjust gap between bars
        plot_bgcolor="rgba(0,0,0,0)",
        hovermode = 'x unified',
        legend_title=None
    )

    fig.update_traces(
        hovertemplate='%{y}',
        marker=dict(
            line=dict(width=0)
        )
    )

    config = dict(
        responsive=True,
        displaylogo=False,
        modeBarButtons= [['toImage']],
    )

    # Display the chart
    return json.dumps(dict(data = fig.data,
                           layout = fig.layout,
                           config = config
                           ), 
                           cls= plotly.utils.PlotlyJSONEncoder)

# Rata-rata usia
def average_age(df):

    first_maintenance = df.drop_duplicates(subset='no_sn')

    prod_client_avg_age = first_maintenance.groupby(['produk_mt', 'nama_client'], observed=False).agg(
        total = ('detail_id', 'count'),
        avg_age = ('selisih_bulan', 'mean')
    ).reset_index()

    product_avg_age = first_maintenance.groupby('produk_mt', observed=False).agg(
        total = ('detail_id', 'count'),
        avg_age = ('selisih_bulan', 'mean')
    ).reset_index()
    product_avg_age['nama_client'] = 'Overall'

    client_avg_age = first_maintenance.groupby('nama_client', observed=False).agg(
        total = ('detail_id', 'count'),
        avg_age = ('selisih_bulan', 'mean')
    ).reset_index()
    client_avg_age['produk_mt'] = 'All Product'

    avg_age = pd.concat([prod_client_avg_age, product_avg_age, client_avg_age], ignore_index=True)
    avg_age.loc[len(avg_age)] = ['All Product', 'Overall', first_maintenance.detail_id.count(), first_maintenance.selisih_bulan.mean()]

    avg_age_pivot = avg_age.pivot(index='nama_client', columns='produk_mt', values='avg_age')

    new_index_order = [idx for idx in avg_age_pivot.index if idx != 'Overall'] + ['Overall']
    avg_age_pivot = avg_age_pivot.reindex(new_index_order)

    new_column_order = [col for col in avg_age_pivot.columns if col != 'All Product'] + ['All Product']
    avg_age_pivot = avg_age_pivot.reindex(columns=new_column_order)

    #Visualization
    fig = px.imshow(
        avg_age_pivot,
        labels=dict(x="Produk", y="Nama Client", color="Avg Age"),
        color_continuous_scale=sequential.Inferno,  # 
        text_auto=".2f"  ,  
        # aspect = 'auto',
        zmin = np.nanmin(avg_age_pivot.values) - 2, 
        zmax = np.nanmax(avg_age_pivot.values) + 2,
    )

    fig.update_layout(
        # autosize=True,
        title="Rata-rata Usia Produk",
        title_x=0.5,
        xaxis=dict(
            fixedrange=True,
            title=None,
        ),
        yaxis=dict(
            fixedrange=True,
            title=None,
        ),
        plot_bgcolor="rgba(0,0,0,0)",
        legend_title=None
    )

    fig.update_traces(
        texttemplate="%{z:.2f}",
        xgap=1,
        ygap=1,
    )

    config = dict(
        responsive=True,
        displaylogo=False,
        modeBarButtons= [['toImage']],
    )

    return json.dumps(dict(data = fig.data,
                           layout = fig.layout,
                           config = config
                           ), 
                           cls= plotly.utils.PlotlyJSONEncoder)

# Usia produk
def product_age(df):
    df = df.drop_duplicates(subset='no_sn')

    fig = px.strip(
        df,
        x= 'produk_mt',
        y= 'selisih_bulan',
        stripmode= 'overlay',
        color= 'nama_client',
        color_discrete_sequence=qualitative.T10,
        labels=dict(
            produk_mt = 'Produk',
            selisih_bulan = 'Usia (Bulan)',
            nama_client = 'Client'
        ),
        category_orders={
            'produk_mt': df['produk_mt'].cat.categories.tolist(),
            'nama_client': sorted(df['nama_client'].unique())
        },
        hover_name='no_sn', 

    )

    fig.update_traces(
        jitter=1, 
        opacity = 0.8,
        hovertemplate='<b>%{hovertext}</b><br><br>Usia: %{y:.2f}'
        )

    fig.update_layout(
        title="Distribusi Usia Produk",
        title_x=0.5,
        xaxis=dict(
            title=None,
            fixedrange=True,
            linecolor = 'lightgrey'
        ),
        yaxis=dict(
            title="Usia (Bulan)",
            fixedrange=True,
            gridcolor = 'lightgrey',
            rangemode="tozero",
            # gridwidth = 1,
        ),
        plot_bgcolor="rgba(0,0,0,0)",
        legend_title=None
    )

    config = dict(
        responsive=True,
        displaylogo=False,
        modeBarButtons= [['toImage']],
    )

    return json.dumps(dict(data = fig.data,
                           layout = fig.layout,
                           config = config
                           ), 
                           cls= plotly.utils.PlotlyJSONEncoder)

def top_10_komponen(df):
    df = df['barang'].value_counts().head(10).reset_index(name='qty')

    fig = px.bar(
        df,
        x='qty',
        y='barang',
        title="Top 10 Komponen",
        # labels={'barang': 'Komponen', 'qty': 'Jumlah Maintenance'},   
    )

    fig.update_layout(
        xaxis=dict(
            fixedrange=True,
            title=None,
            gridcolor='lightgrey',
            linecolor='black'
        ),
        yaxis=dict(
            fixedrange=True,
            title=None,
            categoryorder='total ascending',
            linecolor='black'
        ),
        hoverlabel_bgcolor='black',
        title_x=0.5,
        plot_bgcolor="rgba(0,0,0,0)",
        
    )
    
    fig.update_traces(
        marker=(dict(
            color=[qualitative.Plotly[1] if i == 0 else qualitative.Plotly[0] for i in range(len(df))]
            )
        ),
        hovertemplate='%{x}'
    )

    config = dict(
        responsive=True,
        displaylogo=False,
        modeBarButtons= [['toImage']],
    )

    return json.dumps(dict(data = fig.data,
                           layout = fig.layout,
                           config = config
                           ), 
                           cls= plotly.utils.PlotlyJSONEncoder)

def comparison(df1, df2, col):
    data1 = df1[col].value_counts().reset_index(name='Produksi')
    data2 = df2.groupby(col, observed=True).no_sn.nunique().reset_index(name='Maintenance')

    data = pd.merge(data1, data2, on=col, how='outer').fillna(0)

    cat_orders = df1[col].unique().tolist()
    data[col] = pd.Categorical(data[col], categories=cat_orders, ordered=True)
    data = data.sort_values(by=col)

    data['persentase'] = (data['Maintenance'] / data['Produksi']) * 100

    fig = px.bar(
        data,
        x=col,
        y=['Produksi', 'Maintenance'],
        color='variable',
        title=f"Perbandingan Jumlah {col}",
        # labels={'produksi': 'Produksi', 'maintenance': 'Maintenance', col: col},
        barmode='group',
        color_discrete_map={'Produksi': 'rgb(55, 83, 109)', 'Maintenance': 'rgb(26, 118, 255)'}

    )

    fig.update_layout(
        title='Perbandingan Produksi dan Maintenance',
        xaxis=dict(
            title=None,
            fixedrange=True,
            linecolor='black'
        ),
        yaxis=dict(
            title=None,
            fixedrange=True,
            gridcolor='lightgrey',
            linecolor='black'
        ),
        legend=dict(
            orientation='h',
            x=0.5,
            xanchor='center',
            y=-0.1,
            title=None,
            itemclick=False,
            itemdoubleclick=False,
        ),
        bargap=0.5,
        title_x=0.5,
        plot_bgcolor="rgba(0,0,0,0)",
        # hoverlabel_bgcolor='black',
    )

    fig.update_traces(
        hovertemplate='%{y}',
    )

    config = dict(
        responsive=True,
        displaylogo=False,
        modeBarButtons= [['toImage']],
    )

    return json.dumps(dict(data = fig.data,
                           layout = fig.layout,
                           config = config
                           ), 
                           cls= plotly.utils.PlotlyJSONEncoder)
