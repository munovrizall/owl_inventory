
from flask import Flask, jsonify
from flask_cors import CORS

from visualizations import *

app = Flask(__name__)
CORS(app)

@app.route('/produksi')
def api_produksi(): 
    produksi_df = get_produksi()

    graph_monthly_production = monthly_production(produksi_df)
    graph_product_type_distribution = product_type_distribution(produksi_df)
    graph_product_distribution = product_distribution(produksi_df)
    
    data = {
            'graph_monthly_production': graph_monthly_production,
            'graph_product_type_distribution': graph_product_type_distribution,
            'graph_product_distribution': graph_product_distribution
        }
    
    return jsonify(data)

@app.route('/maintenance')
def api_maintenance():
    produksi_df = get_produksi()
    maintenance_df = get_maintenance()
    komponen_df = get_komponen()

    graph_monthly_maintenance = monthly_maintenance(maintenance_df)
    graph_average_age = average_age(maintenance_df)
    graph_product_age = product_age(maintenance_df)
    graph_component = top_10_komponen(komponen_df)
    graph_comparison = comparison(produksi_df, maintenance_df, 'type_produk')

    data = dict(
        graph_monthly_maintenance = graph_monthly_maintenance,
        graph_average_age = graph_average_age,
        graph_product_age = graph_product_age,
        graph_component = graph_component,
        graph_comparison = graph_comparison
    )
    return jsonify(data)

if __name__ == "__main__":
    app.run(debug=True)
