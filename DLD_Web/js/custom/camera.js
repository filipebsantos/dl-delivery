const player = document.getElementById('player');
const captureButton = document.getElementById('captureButton');
const canvas = document.getElementById('canvas');
const closeModalButton = document.getElementById('closeModal');
const videoContainer = document.getElementById('videoContainer');
const canvasContainer = document.getElementById('canvasContainer');
const photoInput = document.getElementById('capturedPhoto');

// Configure video constraints
const constraints = {
    video: {
        width: 600, // Invert aspect to mobile's camera capture horizontal photos
        height: 800,
        facingMode: {
            exact: 'environment'
        }
    },
    audio: false,
};

function stopCameraStream() {
    const stream = player.srcObject;
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
    }
}

function startCameraStream() {
    canvasContainer.style.display = 'none'; // Hide canva display
    player.style.display = 'inline'; // Show camera display

    navigator.mediaDevices.getUserMedia(constraints).then((stream) => {
        player.srcObject = stream;
    });
}

// Start camera on modal load or show captured photo
document.getElementById('cameraModal').addEventListener('focus', () => {
    if (photoInput.value != "") {
        let image = new Image();
        image.src = photoInput.value;

        image.onload = () => {
            const context = canvas.getContext('2d');
            context.clearRect(0, 0, canvas.width, canvas.height);
            context.drawImage(image, 0, 0, canvas.width, canvas.height);
            canvasContainer.style.display = 'inline';
        };
    } else {
        startCameraStream();
    }
});

// Capture Button EventListener
captureButton.addEventListener('click', () => {

    // Prepare to take another picture in case already exists a photo
    if (photoInput.value != "") {
        canvasContainer.style.display = 'none';
        videoContainer.style.display = 'inline';

        startCameraStream();
        photoInput.value = "";
    } else {
        const context = canvas.getContext('2d');
        context.drawImage(player, 0, 0, canvas.width, canvas.height);
        const dataUrl = canvas.toDataURL('image/webp');
        photoInput.value = dataUrl;

        videoContainer.style.display = 'none';
        canvasContainer.style.display = 'inline';
        stopCameraStream();
    }
});

// Stop camera stream when modal is closed
closeModalButton.addEventListener('click', () => {
    stopCameraStream();
    videoContainer.style.display = 'inline';
    canvasContainer.style.display = 'none';
    player.style.display = 'none';
    canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
});
