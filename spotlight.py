import json
import spotlight
from flask import Flask, request
from flask.views import MethodView


class SpotlightApi(MethodView):
    def get(self):
        return 'AmebyDevGroup DBPedia Spotlight API'

    def post(self):
        response_data = spotlight.annotate('https://api.dbpedia-spotlight.org/en/annotate', request.json.get('data'))
        response = app.response_class(
            response=json.dumps(response_data),
            status=200,
            mimetype='application/json'
        )
        return response


app = Flask(__name__)
spotlight_view = SpotlightApi.as_view('spotlight_api')
app.add_url_rule('/', view_func=spotlight_view, methods=['GET', 'POST'])

if __name__ == '__main__':
    app.run('0.0.0.0')
