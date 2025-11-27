# decode_image, image processing utils
import requests
import numpy as np
import cv2

def decode_image(image_url):
    try:
        resp = requests.get(image_url)
        if resp.status_code == 200:
            return cv2.imdecode(np.frombuffer(resp.content, np.uint8), cv2.IMREAD_COLOR)
        else:
            print(f"Failed to retrieve image: {image_url}")
            return None
    except Exception as e:
        print(f"Error decoding image: {e}")
        return None
                                                        