const likeButtonElem = document.getElementsByClassName('like-button');

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