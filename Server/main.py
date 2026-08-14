from flask import Flask, request, jsonify
from flask_cors import CORS
from transformers import AutoTokenizer, AutoModelForSeq2SeqLM
import os

app = Flask(__name__)
CORS(app)

# Force fully offline — never attempt any download or network check
os.environ["TRANSFORMERS_OFFLINE"] = "1"
os.environ["HF_DATASETS_OFFLINE"] = "1"

# Use the locally cached model. If you saved it to a specific folder, replace
# this with the absolute path, e.g. r"C:\nllb_model"
MODEL_PATH = "facebook/nllb-200-distilled-600M"

print("Loading model into memory (offline mode)...")
tokenizer = AutoTokenizer.from_pretrained(MODEL_PATH, local_files_only=True)
model = AutoModelForSeq2SeqLM.from_pretrained(MODEL_PATH, local_files_only=True)
print("Model loaded successfully!")

@app.route('/translate', methods=['POST'])
def translate():
    data = request.get_json()

    text     = data.get('text', '')
    src_lang = data.get('src_lang', 'eng_Latn')
    tgt_lang = data.get('tgt_lang', 'fra_Latn')

    if not text:
        return jsonify({'error': 'No text provided'}), 400

    # Set source language
    tokenizer.src_lang = src_lang

    # Tokenize input
    inputs = tokenizer(text, return_tensors="pt")

    # Force target language token
    tgt_lang_id = tokenizer.convert_tokens_to_ids(tgt_lang)
    if tgt_lang_id == tokenizer.unk_token_id:
        return jsonify({'error': f'Unsupported target language: {tgt_lang}'}), 400

    # Generate translation
    translated_tokens = model.generate(
        **inputs,
        forced_bos_token_id=tgt_lang_id,
        max_length=512
    )

    result = tokenizer.batch_decode(translated_tokens, skip_special_tokens=True)[0]
    return jsonify({'translated_text': result})

if __name__ == '__main__':
    app.run(host='127.0.0.1', port=5000, debug=False)