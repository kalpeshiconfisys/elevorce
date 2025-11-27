 # All API calls (get_all_images_data, get_new_user, etc.)

import requests
from config import *
from .image_service import decode_image

def _get_json(url):
    try:
        response = requests.get(url)
        response.raise_for_status()
        return response.json()
    except Exception as e:
        print(f"Error fetching {url}: {e}")
        return None

# def _post_json(url, data):
#     try:
#         response = requests.post(url, json=data)
#         response.raise_for_status()
#         return response.json()
#     except Exception as e:
#         print(f"Error posting to {url}: {e}")
#         return None

def get_user_profile():
    user_data = _get_json(GET_USER_PROFILE_API)

    if user_data['success']:
        for data in user_data['data']:
            user_id = data['id']
            selfie_url = data['selfie']
            yield user_id, selfie_url
        return None, None




