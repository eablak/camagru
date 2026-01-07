const likeButtonElem = document.getElementsByClassName('like-button');
const commentButtonElem = document.getElementsByClassName('comment-button');

for (var i=0; i<likeButtonElem.length; i++){
    likeButtonElem[i].addEventListener('click', function(){
        
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "like");
        xhr.setRequestHeader("Content-type", 'application/json');

        const imageId = this.closest(".image-container").dataset.imageId;
        JsonResponse = {"imageId" : imageId};

        xhr.send(JSON.stringify(JsonResponse));

        this.style.backgroundColor = "grey";

    })
}

for (var j=0; j<commentButtonElem.length; j++){
    commentButtonElem[j].addEventListener('click', function(){

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "comment");
        xhr.setRequestHeader("Content-type", 'application/json');

        const relativeImage = this.closest(".image-container");
        const imageId = relativeImage.dataset.imageId;
        const commentText = relativeImage.querySelector("textarea").value;

        commentJson = {"imageId": imageId, "commentText": commentText};
        xhr.send(JSON.stringify(commentJson));

        relativeImage.querySelector("textarea").value = "";
    })
}