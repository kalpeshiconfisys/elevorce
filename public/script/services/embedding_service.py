# embedding_exists, store_embeddings, generate_embeddings

import numpy as np
import requests
from config import *

def generate_embeddings(image, model):
    faces = model.get(image)
    return [face.normed_embedding for face in faces]

# def embedding_exists(id, is_user=True):
#     api_url = USER_EMBEDDING_EXISTS_API if is_user else IMG_EMBEDDING_EXISTS_API
#     try:
#         resp = requests.get(f"{api_url}{id}")
#         resp.raise_for_status()
#         data = resp.json()
#         if data.get("success"):
#             embeddings = [np.array(e, dtype=np.float32) for e in json.loads(data['data']['embedding_img'])]
#             return True, embeddings
#         return False, []
#     except Exception as e:
#         print(f"Unable to check whether the embedding exists or not: {e}")
#         return False, []

def store_embeddings(id, embeddings):
    embeddings_list = [e.tolist() for e in embeddings if isinstance(e, np.ndarray)]
    # print(embeddings_list)
    data = {
        "employee_id" : id,
        "embedding": embeddings_list
        }
    # print(f"Sending data: {data}")
    try:
        response = requests.post(USER_EMBEDDING_STORE_API, json=data)
        if response.json().get("success"):
            print(response.json())
            print("Embeddings stored successfully")
            print("-" * 100)
        else:
            print("Failed to send embeddings")
            print(f"Response:{response.json()}")
    except Exception as e:
       print(f"Error storing embeddings: {e}")



