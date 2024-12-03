<?php
require_once "Note.php";

class TextNote extends Note {
    private ?string $content;

    public function __construct(
        int $id, 
        string $title, 
        int $owner, 
        DateTime $created_at, 
        bool $pinned, 
        bool $archived, 
        int $weight, 
        ?DateTime $edited_at
    ) {
        parent::__construct($id, $title, $owner, $created_at, $pinned, $archived, $weight, $edited_at);
        $this->content = $this->getContentFromDatabase();
    }


    
    // Fonction pour tronquer le contenu d'une note si trop long
    private function truncateContent($content, $maxLength = 100) {
        if ($content == null) 
            return$content;
        if (strlen($content) > $maxLength) {
            // Tronquer le contenu et ajouter des points de suspension à la fin
            $truncatedContent = substr($content, 0, $maxLength) . '...';
            return $truncatedContent;
        } else {
            // Retourner le contenu tel quel s'il est plus court que la longueur maximale
            return $content;
        }
    }

    public function getTruncateContent() {
        return $this->truncateContent($this->content);
    }

    public function getContent(): ?string {
        return $this->content;
    }
    public function setContent(string $content) {
        $this->content = $content;
    }

    private function getContentFromDatabase(): ?string {
        $noteId = $this->getId();

        $query = self::execute("SELECT content FROM text_notes WHERE id = :noteId", ["noteId" => $noteId]);
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        } else {
            return $data['content'];
        }
    }
    

    public function getTextNoteIdFromDatabase(): int {
        $noteId = $this->getId();

        $query = self::execute("SELECT id FROM text_notes WHERE id = :noteId", ["noteId" => $noteId]);
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return 0;
        } else {
            return $data['id'];
        }
    }

    public function persist(): TextNote {
        parent::persist();

        $noteId = Note::lastInsertId();
        self::execute("INSERT INTO text_notes(id, content) VALUES(:noteId, :content)", [
            "noteId" => $noteId,
            "content" => $this->content
        ]);

        return $this;
    }
    
    public function update($editedTitle, $editedContent){
        self::execute("UPDATE text_notes SET content = :editedContent WHERE id = :id", [
            "id" => $this->id,
            "editedContent" => $editedContent
        ]);
    
        $editionDate = new DateTime();
        self::execute("UPDATE notes SET title = :editedTitle, edited_at = :editionDate WHERE id = :id", [
            "id" => $this->id,
            "editedTitle" => $editedTitle,
            "editionDate" => $editionDate->format('Y-m-d H:i:s') 
        ]);
    }
    
    public function deleteNote() : void{
        $noteId = $this->id;
        self::execute("DELETE FROM note_labels WHERE note = :noteId ",
                        ["noteId" => $noteId]);
        self::execute("DELETE FROM note_shares WHERE note = :noteId ",
                        ["noteId" => $noteId]);
        self::execute("DELETE FROM text_notes WHERE id = :noteId ",
                        ["noteId" => $noteId]);
        self::execute("DELETE FROM notes WHERE id = :noteId ",
                        ["noteId" => $noteId]);
        
    }
    
}
