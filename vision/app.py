from fastapi import FastAPI, UploadFile, File
from fastapi.responses import JSONResponse
from ultralytics import YOLO
from PIL import Image
import io

app = FastAPI(title="Vision Service - YOLOv8")

# Charge le modele YOLOv8 pre-entraine (nano = le plus leger/rapide)
# Le fichier sera telecharge automatiquement au premier lancement.
model = YOLO("yolov8n.pt")

@app.get("/health")
def health():
    return {"status": "ok"}

@app.post("/detect")
async def detect(file: UploadFile = File(...)):
    image_bytes = await file.read()
    image = Image.open(io.BytesIO(image_bytes)).convert("RGB")

    results = model(image)
    detections = []

    for result in results:
        for box in result.boxes:
            cls_id = int(box.cls[0])
            label = model.names[cls_id]
            confidence = float(box.conf[0])
            x1, y1, x2, y2 = [int(coord) for coord in box.xyxy[0].tolist()]
            detections.append({
                "label": label,
                "confidence": round(confidence, 3),
                "box": {"x1": x1, "y1": y1, "x2": x2, "y2": y2}
            })

    # Trie par confiance decroissante, garde la detection la plus fiable
    detections.sort(key=lambda d: d["confidence"], reverse=True)

    return JSONResponse({
        "detections": detections,
        "best": detections[0] if detections else None,
        "image_size": {"width": image.width, "height": image.height}
    })
