import os
from contextlib import redirect_stdout
from insightface.app import FaceAnalysis

# Redirecting the output of loading the models to a system file where everything is discarded
def load_models():
    with open(os.devnull, 'w') as f:
        with redirect_stdout(f):
            # Initializing 3 FaceAnalysis models for CPU-only with different det_size
            # If GPU (ctx_id=0) else CPU (ctx_id=-1)
            app_800 = FaceAnalysis(name="buffalo_l", providers=['CPUExecutionProvider'])
            app_800.prepare(ctx_id=0, det_size=(800, 800))

            # app_480 = FaceAnalysis(name="buffalo_l", providers=['CPUExecutionProvider'])
            # app_480.prepare(ctx_id=0, det_size=(480, 480))

            return app_800
