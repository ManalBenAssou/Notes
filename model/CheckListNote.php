<?php
require_once "Note.php";
require_once "CheckListNoteItem.php";

class CheckListNote extends Note {
    private array $items;

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
    }
   

    public function getContent(): string {
        // Dans cet exemple, on concatène le contenu des items
        return implode(", ", array_map(fn ($item) => $item->content, $this->items));
    }

    public function getCheckedItemsCount() {
        $count = 0;
        foreach($this->getChecklistItems() as $item) {
            if($item->isChecked()) {
                ++$count;
            }
        }
        return $count;
    }

    public function getChecklistItems(): array {
        $noteId = $this->getId();
        $query = self::execute("SELECT * FROM checklist_note_items WHERE checklist_note = :noteId ORDER BY id desc", ["noteId" => $noteId]);
        $data = $query->fetchAll();
        $items = [];

        foreach ($data as $row) {
            $checkListItem = new CheckListNoteItem(
                $row['id'],
                $row['checklist_note'],
                $row['content'],
                (bool)$row['checked']
            );

            $items[] = $checkListItem;
        }

        return $items;
    }

    public function setChecklistItems(array $items) {
        
        foreach ($items as $item) {
            $checkListItem = new CheckListNoteItem(
                0,
                $this->getId(),
                $item,
                false
            );
            $checkListItem->persist();
        }
        
    }
    

    public static function getLastId() : int {
        $query = self::execute("SELECT MAX(id) as max_id FROM checklist_notes", []);
        $data = $query->fetch();
    
        return (int)$data['max_id'];
    }

    public function persist(): CheckListNote {
        parent::persist();

        $noteId = Note::lastInsertId();
        self::execute("INSERT INTO checklist_notes(id) VALUES(:noteId)", [
            "noteId" => $noteId,
        ]);


        return $this;
    }

    public function deleteNote() : void {
        $noteId = $this->id;
        
        self::execute("DELETE FROM note_labels WHERE note = :noteId ",
        ["noteId" => $noteId]);
        self::execute("DELETE FROM note_shares WHERE note = :noteId ",
        ["noteId" => $noteId]);
        self::execute("DELETE FROM checklist_note_items WHERE checklist_note = :noteId ",
        ["noteId" => $noteId]);
        self::execute("DELETE FROM checklist_notes WHERE id = :noteId ",
        ["noteId" => $noteId]);
        self::execute("DELETE FROM notes WHERE id = :noteId ",
        ["noteId" => $noteId]);
    }
    
    public function deleteAllItems($noteId): void{
        self::execute("DELETE FROM checklist_note_items WHERE checklist_note = :noteId ",
        ["noteId" => $noteId]);
    }
    public function update($title) : void {
        self::execute("UPDATE notes SET title= :title WHERE id=:id",
        ["title"=>$title, 
        "id"=>$this->id
        ]);

        $editionDate = new DateTime();
        self::execute("UPDATE notes SET title = :title, edited_at = :editionDate WHERE id = :id", [
            "id" => $this->id,
            "title" => $title,
            "editionDate" => $editionDate->format('Y-m-d H:i:s') 
        ]);
           
    }
}
