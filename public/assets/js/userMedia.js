const videoElem = document.getElementById('video');
const errorElem = document.getElementById('error');
const catElem = document.getElementById('cat');
const mouseElem = document.getElementById('mouse');
const hamsterElem = document.getElementById('hamster');
const dogElem = document.getElementById('dog');
const duckElem = document.getElementById('duck');
const imgPositionElem = document.getElementById('superposable-img');
const radElem = document.getElementsByName('obj');

let receivedMediaStream = null;
var prev = null;

for(var i=0; i<radElem.length; i++){
    radElem[i].addEventListener('change', function() {
        (prev) ? console.log(prev.value): null;
        if (this !== prev)
            prev = this;
        console.log(this.value)
    })
}

const constraints = {
    audio: false,
    video: true
}

function openCamera() {
    navigator.mediaDevices.getUserMedia(constraints)
        .then(mediaStream => {
            videoElem.srcObject = mediaStream;
            receivedMediaStream = mediaStream;
            errorElem.innerHTML = "";
        }).catch(err => {
            errorElem.innerHTML = err;
            errorElem.style.display = "block";
        });
}

const closeCamera = () => {
    if (!receivedMediaStream) {
        errorElem.innerHTML = "Camera is already closed!";
        errorElem.style.display = "block";
    } else {
        receivedMediaStream.getTracks().forEach(mediaTrack => {
            mediaTrack.stop();
        });
        errorElem.innerHTML = "Camera closed successfully!";
        errorElem.style.display = "block";
        videoElem.srcObject = null;
    }
}

function captureImage() {

    var selectedRadio = document.getElementsByName('obj');
    var isSelected = false;
    var nameSelected = "";
    
    for(i=0; i<selectedRadio.length; i++){
        if (selectedRadio[i].checked){
            document.getElementById("error").innerHTML = selectedRadio[i].value;
            isSelected = true;
            nameSelected = selectedRadio[i].value;
        }
    }

    if (!isSelected && !receivedMediaStream){
        document.getElementById("error").innerHTML = "Open camera and choose superposable image!"
        return;
    }
    
    if (!isSelected){
        document.getElementById("error").innerHTML = "Please choose superposable image first!";
        return;
    }
    
    if (!receivedMediaStream) {
        errorElem.innerHTML = "Open the camera first!";
        errorElem.style.display = "block";
        return;
    }

    var path = "/assets/img/thumbnails/";
    switch(nameSelected){
        case 'cat':
            path += 'img1.png';
        break;
        case 'mouse':
            path += 'img2.png';
        break;
        case 'hamster':
            path += 'img3.png';
        break;
        case 'dog':
            path += 'img4.png';
        break;
        case 'duck':
            path += 'img5.png';
        break;
    }

    imgPositionElem.src = path;


}