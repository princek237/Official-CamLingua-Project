from flask import Flask, request, jsonify
from flask_cors import CORS
from transformers import AutoTokenizer, AutoModelForSeq2SeqLM

app = Flask(__name__)
CORS(app)  # Allows your HTML/JS frontend to talk to this server

# Path where your downloaded model and tokenizer are saved locally
# If downloaded via default Hugging Face cache, use "facebook/nllb-200-distilled-600M"
# If saved in a local folder, use the folder path (e.g., "./nllb_model")
MODEL_PATH = "facebook/nllb-200-distilled-600M" 

print("Loading model into memory...")
tokenizer = AutoTokenizer.from_pretrained(MODEL_PATH)
model = AutoModelForSeq2SeqLM.from_pretrained(MODEL_PATH)
print("Model loaded successfully!")

@app.route('/translate', methods=['POST'])
def translate():
    data = request.get_json()
    
    text = data.get('text', '')
    src_lang = data.get('src_lang', 'eng_Latn')  # Default English
    tgt_lang = data.get('tgt_lang', 'fra_Latn')  # Default French

    if not text:
        return jsonify({'error': 'No text provided'}), 400

    # Set source language
    tokenizer.src_lang = src_lang

    # Tokenize input
    inputs = tokenizer(text, return_tensors="pt")

    # Force target language ID
    tgt_lang_id = tokenizer.convert_tokens_to_ids(tgt_lang)

    # Generate output
    translated_tokens = model.generate(
        **inputs,
        forced_bos_token_id=tgt_lang_id,
        max_length=256
    )

    # Decode tokens back into human text
    result = tokenizer.batch_decode(translated_tokens, skip_special_tokens=True)[0]

    return jsonify({'translated_text': result})

if __name__ == '__main__':
    app.run(host='127.0.0.1', port=5000)