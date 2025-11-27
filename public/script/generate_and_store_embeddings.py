# from datetime import datetime 
# from config import USER_ID_PARAM
from models.face_models import load_models
from services.api_service import get_user_profile
from services.embedding_service import store_embeddings, generate_embeddings
from services.image_service import decode_image
import time

def main():
    # start_time = datetime.now()
    # print(f"Start time: {start_time}")

    model_800 = load_models()
    print("Model Loaded successfully...")

    for user_id, selfie_url in get_user_profile():
    # user_id, selfie_url = get_user_profile()
        print(user_id, selfie_url)
        if not user_id or not selfie_url:
            print("No user data found.")
            return
        user_image = decode_image(selfie_url)
        # print(user_image)

        user_embeddings = generate_embeddings(user_image, model_800)
        # print(user_embeddings)
        store_embeddings(user_id, user_embeddings)
        time.sleep(1)

if __name__ == '__main__':
    main()
    