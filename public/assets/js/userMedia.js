const videoElem = document.getElementById('video');
const errorElem = document.getElementById('error');
let receivedMediaStream = null;

//Declare the MediaStreamConstraints object
const constraints = {
    audio: true,
    video: true
}

function openCamera() {
//Ask the User for the access of the device camera and microphone
    navigator.mediaDevices.getUserMedia(constraints)
        .then(mediaStream => {
            // The received mediaStream contains both the 
            // video and audio media data

//Add the mediaStream directly to the source of the video element
            // using the srcObject attribute
            videoElem.srcObject = mediaStream;

            // make the received mediaStream available globally
            receivedMediaStream = mediaStream;

        }).catch(err => {
            // handling the error if any
            errorElem.innerHTML = err;
            errorElem.style.display = "block";
        });

}


const closeCamera = () => {
    if (!receivedMediaStream) {
        errorElem.innerHTML = "Camera is already closed!";
        errorElem.style.display = "block";
    } else {
/* MediaStream.getTracks() returns an array of all the 
MediaStreamTracks being used in the received mediaStream
we can iterate through all the mediaTracks and 
stop all the mediaTracks by calling its stop() method*/
        receivedMediaStream.getTracks().forEach(mediaTrack => {
            mediaTrack.stop();
        });
        errorElem.innerHTML = "Camera closed successfully!"
        errorElem.style.display = "block";
    }
}