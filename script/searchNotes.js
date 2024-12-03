$(document).ready(function() {
    $(".labels-checkbox").on("change", function(event) {
        event.preventDefault();
        var checkedLabels = [];
        // Parcourez tous les éléments cochés
        $(".labels-checkbox:checked").each(function() {
            checkedLabels.push($(this).val()); // Ajoutez la valeur de l'élément cochée au tableau
        });

        console.log("labels: " + checkedLabels);

        $.ajax({
            url: "note/searchNotesJs",
            type: "POST",
            data: { checkedLabels: checkedLabels },
            dataType: "json",
            success: function(response) {
                console.log("reponse : " +response);
                console.log("my notes tab "+response.myNotes);
                console.log("sharedNotes: "+ response.allSharedNotes);
                
                updateMyNotes(response.myNotes);
                updateSharedNotes(response.allSharedNotes);
                
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });
    });
    
    // Cacher le bouton search lorsque JavaScript est activé
    window.onload = function() {
        document.getElementById('buttonSearch').style.display = 'none';
    };
});
function updateMyNotes(myNotes) {
    let htmlMyNotes = '';
    if (myNotes.length != 0)
        htmlMyNotes += ` <h5>Your notes : </h5> `;
    myNotes.forEach(note => {
        let contentHtml = '';

        if (note.noteType === 'ChecklistNote') {
            contentHtml = '<ul style="list-style-type: none;">';
            note.items.forEach(item => {
                contentHtml += `
                    <li>
                        <label>
                            <input type="checkbox" disabled ${item.checked? 'checked' : ''}>
                            ${item.itemContent}
                        </label>
                    </li>
                `;
            });
            contentHtml += '</ul>';
        } else if (note.noteType === 'TextNote') {
            contentHtml = `<div>${note.content}</div>`;
        }
        console.log(note.encodedUrl);

        htmlMyNotes += `
            <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                <a href="note/openNote/${note.noteId}/${note.encodedUrl}" class="note-link row">                

                    <div class="note-row card">
                        <div class="note-title card-header">${note.title}</div>
                        <div class="note-content card-body">${contentHtml}</div>
                        <div class="note-labels">
                            ${note.labels.map(label => `<div class="label">${label}</div>`).join('')}
                        </div>
                    </div>
                </a>
            </div>
        `;
    });

    $('.my-notes').html(htmlMyNotes); // Utilisez jQuery pour mettre à jour le HTML
}

function updateSharedNotes(allSharedNotes) {
    // pour supprimer les doublons de allSharedNotes
    const uniqueNotesMap = {};
    const uniqueNotes = [];

    allSharedNotes.forEach(note => {
        if (!uniqueNotesMap[note.noteId]) {
            uniqueNotesMap[note.noteId] = true;
            uniqueNotes.push(note);
        }
    });

    // Mise à jour de allSharedNotes avec les notes uniques
    allSharedNotes = uniqueNotes;

    let html = '';
    allSharedNotes.forEach(note => {
        
        let contentHtml = '';
        if (note.noteType === 'ChecklistNote') {
            contentHtml = '<ul style="list-style-type: none;">';
            note.items.forEach(item => {
                contentHtml += `<li><label><input type="checkbox" disabled ${item.checked ? 'checked' : ''}> ${item.itemContent}</label></li>`;
            });
            contentHtml += '</ul>';
        } else if (note.noteType === 'TextNote') {
            contentHtml = `<div>${note.content}</div>`;
        }

        html += `
            <div class="col-lg-4 col-md-6 col-sm-12 mb-3">
                <h5>Notes shared by ${note.sharedUserName}</h5>
                <a href="note/openNote/${note.noteId}/${note.encodedUrl}" class="note-link row">
                    <div class="note-row card">
                        <div class="note-title card-header">${note.title}</div>
                        <div class="note-content card-body">${contentHtml}</div>
                        <div class="note-labels">${note.labels.map(label => `<div class="label">${label}</div>`).join('')}</div>
                    </div>
                </a>
            </div>
        `;
    });
    $('#div-shared-note').html(html);
}
