const videoElem = document.getElementById('video');
const errorElem = document.getElementById('error');
const catElem = document.getElementById('cat');
const mouseElem = document.getElementById('mouse');
const hamsterElem = document.getElementById('hamster');
const dogElem = document.getElementById('dog');
const duckElem = document.getElementById('duck');
const imgPositionElem = document.getElementById('superposable-img');
const radElem = document.getElementsByName('obj');
let canvas = document.querySelector('#canvas');
let dataurl = document.querySelector('#dataurl');
let dataurl_container = document.querySelector('#dataurl-container');
const informElem = document.getElementById('inform');
const captureButtonElem = document.getElementById('captureButton');

let receivedMediaStream = null;
var prev = null;
let isUpload = null;

for(var i=0; i<radElem.length; i++){
    radElem[i].addEventListener('change', function() {
        if (this !== prev)
            prev = this;
        // console.log(this.value);
        updatePath(this.value);
        captureButtonElem.style.backgroundColor = "";
        captureButtonElem.disabled = false;
    })
}

function updatePath(nameSelected){

    var path = "/assets/img/superposable/";
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
    imgPositionElem.style.display = "block";

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
        errorElem.style.color = "green";
        errorElem.style.display = "block";
    }
    videoElem.srcObject = null;
    receivedMediaStream = null;
    imgPositionElem.src = "";
    imgPositionElem.style.display = "none";
}

function captureImage() {

    var selectedRadio = document.getElementsByName('obj');
    var isSelected = false;
    var nameSelected = "";
    
    for(i=0; i<selectedRadio.length; i++){
        if (selectedRadio[i].checked){
            // document.getElementById("error").innerHTML = selectedRadio[i].value;
            isSelected = true;
            nameSelected = selectedRadio[i].value;
        }
    }

    if (!isSelected && !receivedMediaStream && !isUpload){
        errorElem.innerHTML = "Open camera or upload any image and choose superposable image!";
        errorElem.style.color = "red";
        errorElem.style.display = "block";
        return;
    }
    
    if (!isSelected){
        errorElem.innerHTML = "Please choose superposable image first!";
        errorElem.style.color = "red";
        errorElem.style.display = "block";
        
        return;
    }
    
    if (!receivedMediaStream && !isUpload) {
        errorElem.innerHTML = "Open the camera or select image first!";
        errorElem.style.color = "red";
        errorElem.style.display = "block";
        return;
    }

    let image_data_url = null;
    if (!isUpload){
        canvas.getContext('2d').drawImage(videoElem, 0, 0, canvas.width, canvas.height);
        image_data_url = canvas.toDataURL('image/jpeg');
    }

    
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "editing");
    xhr.setRequestHeader("Content-type", 'application/json');


    xhr.onload = function(){

        const response = JSON.parse(xhr.responseText);
        errorElem.style.color = "green";
        errorElem.innerHTML = response.message;
    }

    if (!isUpload){
        json_data = {"webcam": image_data_url, "superposable": nameSelected, "csrf_token": window.CSRF_TOKEN};
    }else{
        json_data = {"superposable": nameSelected, "csrf_token": window.CSRF_TOKEN};
    }

    xhr.send(JSON.stringify(json_data));
}


document.addEventListener('DOMContentLoaded', function(){

    const form = document.querySelector('form[enctype="multipart/form-data"]');

    form.addEventListener('submit', function(e){
        e.preventDefault();
        handleFileUpload();
    });

});


function handleFileUpload(){

    const fileInput = document.getElementById('fileToUpload');

    const formData = new FormData();
    formData.append('fileToUpload', fileInput.files[0]);
    formData.append('actionFileUpload', '1');
    formData.append('csrf_token', window.CSRF_TOKEN);
    isUpload = true;

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/editing");

    xhr.onload = function(){

        const response = JSON.parse(xhr.responseText);
        informElem.innerHTML = response.message;

    }

    xhr.send(formData);

}

let x = document.querySelectorAll(".btn-danger");
let j;
for (j=0; j<x.length; j++){
    x[j].addEventListener("click", function(){
        this.style.backgroundColor = "black";
        this.disabled = true;

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "delete");
        xhr.setRequestHeader("Content-type", 'application/json');

        imgJson = {"imgData": this.closest("div").dataset.image, "csrf_token": window.CSRF_TOKEN};
        xhr.send(JSON.stringify(imgJson));

    })
}


