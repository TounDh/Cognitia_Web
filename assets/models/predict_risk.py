import sys
import json

def predict_risk(input_data):
    historique_retards = input_data['Historique_Retards']
    montant_total = input_data['Montant_Total']
    nombre_annulations = input_data['Nombre_Annulations']

    # Your risk assessment logic
    if historique_retards > 2 or nombre_annulations > 1 or montant_total > 1000:
        risk_level = 'High Risk'
    elif historique_retards > 1 or nombre_annulations > 0 or montant_total > 500:
        risk_level = 'Moderate Risk'
    else:
        risk_level = 'Low Risk'

    # Ensure JSON output
    return {
        'risk_level': risk_level,
        'probabilities': [0.37, 0.03, 0.6]  # Example probabilities
    }

# Read input from command line
if __name__ == '__main__':
    try:
        # Parse the input JSON
        input_json = sys.argv[1]
        input_data = json.loads(input_json)
        
        # Predict risk and output as JSON
        result = predict_risk(input_data)
        print(json.dumps(result))
    except Exception as e:
        # Error handling
        print(json.dumps({
            'risk_level': 'Unable to determine risk',
            'error': str(e)
        }))