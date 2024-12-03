<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Delete Note</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+Knujsl7/1L_dstPt3HV5HzF6Gvk/e3s4Wz6iJgD/+ub2oU" crossorigin="anonymous">
        <link href="css/messageConfirmation.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class="modal-background">
            <div class="modal-container">
                <div class="modal-header">
                    <div class="modal-icon">
                        <i class="bi bi-trash" style="font-size: xxx-large;color:crimson"></i>
                    </div>
                    <h2 class="modal-title">Are you sure?</h2>
                    <hr>
                </div>
                <div class="modal-body">
                    <p>Do you really want to delete note "<span><?php echo $note ->getTitle()?></span>" and all of its dependencies?</p>
                    <p>This process cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <form method="post" action="note/deleteNote/<?=$note->getId()?>">
                        <button type="submit" class="btn btn-danger" style="color: white">Delete</button>
                    </form>
                    <a href="note/openNote/<?=$note->getId()?>" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </body>
</html>