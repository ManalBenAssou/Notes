<?php

class CheckListNoteItem extends Model{
    private int $id;
    private int $checklistNoteId;
    private string $content;
    private bool $checked;

    public function __construct(int $id, int $checklistNoteId, string $content, bool $checked) {
        $this->id = $id;
        $this->checklistNoteId = $checklistNoteId;
        $this->content = $content;
        $this->checked = $checked;
    }

    public function getId(): int {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
    }

    public function getChecklistNoteIdM() : int {
        return $this->checklistNoteId;
    }

    public function getContent(): string {
        return $this->content;
    }

    public function setContent(array $items) {
        foreach ($items as $item){
            $this->content = $item;
        }
    }

    public function isChecked(): bool {
        return $this->checked;
    }

    public function checkIem($isChecked) {
        $this->checked = $isChecked;
    }

    public function equals(CheckListNoteItem $otherItem): bool {
        return (
            $this->getId() === $otherItem->getId() &&
            $this->getChecklistNoteIdM() === $otherItem->getChecklistNoteIdM() &&
            $this->getContent() === $otherItem->getContent() &&
            $this->isChecked() === $otherItem->isChecked()
        );
    }

    public static function getItemById($itemId) : ?CheckListNoteItem {
        $query = self::execute("SELECT * FROM checklist_note_items WHERE id = :itemId", ["itemId" => $itemId]);
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        }
        else {
            return new CheckListNoteItem(
                $data['id'],
                $data['checklist_note'],
                $data['content'],
                $data['checked']
            );
        }
    }
    public static function getItemByName(string $itemName, int $noteId): ?CheckListNoteItem {
        //$noteId = $this->getNoteId();
        $query = self::execute("SELECT * FROM checklist_note_items WHERE checklist_note = :noteId AND content = :itemName", [
            "noteId" => $noteId,
            "itemName" => $itemName,
        ]);
        $data = $query->fetch();
    
        if ($data) {
            return new CheckListNoteItem(
                $data['id'],
                $data['checklist_note'],
                $data['content'],
                (bool)$data['checked']
            );
        }
    
        return null;
    }

    public function save($id, $isChecked) {
        $checkedValue = $isChecked ? 1 : 0;
        self::execute("UPDATE checklist_note_items SET checked = :isChecked WHERE id = :id", ["id" => $id, "isChecked" => $checkedValue]);
    }

    public function persist(): CheckListNoteItem {      
        //$noteId = CheckListNote::lastInsertId();
        $noteId = CheckListNote::getLastId();
        $checkedValue = $this->checked ? 1 : 0;
        self::execute("INSERT INTO checklist_note_items(id, checklist_note, content, checked) VALUES(:id, :noteId, :content, :checked)", 
        [
            "id" => $this->id,
            "noteId" => $noteId,
            "content" => $this->content,
            "checked" => $checkedValue

        ]);

        return $this;
    }

    public function getChecklistNoteId($itemId) {
        $query = self::execute("SELECT DISTINCT checklist_note FROM checklist_note_items WHERE id = :itemId", ["itemId" => $itemId]);
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return null;
        }
        else {
            return $data['checklist_note'];    
        }
    }
    public function addNewItem(){
        $noteId = $this->checklistNoteId;
        $checkedValue = $this->checked ? 1 : 0;
        self::execute("INSERT INTO checklist_note_items(id, checklist_note, content, checked) VALUES(:id, :noteId, :content, :checked)", 
        [
            "id" => $this->id,
            "noteId" => $noteId,
            "content" => $this->content,
            "checked" => $checkedValue
        ]);
        $editionDate = new DateTime();
        self::execute("UPDATE notes SET edited_at = :editionDate WHERE id = :noteId", [
            "noteId" => $noteId,
            "editionDate" => $editionDate->format('Y-m-d H:i:s') 
        ]);
    }
    public function checkItemExists($noteId, $content) {
        $result = self::execute("SELECT * FROM checklist_note_items WHERE checklist_note = :noteId AND content = :content", 
        [
            "noteId" => $noteId,
            "content" => $content
        ]);
        return $result->rowCount() > 0;
    }
    public function deleteItem(){
        self::execute("DELETE FROM checklist_note_items WHERE  id = :id",
        ["id" => $this->id]);
        $noteId = $this->checklistNoteId;
        $editionDate = new DateTime();
        self::execute("UPDATE notes SET edited_at = :editionDate WHERE id = :noteId", [
            "noteId" => $noteId,
            "editionDate" => $editionDate->format('Y-m-d H:i:s') 
        ]);
    }
    public function updateContent($content) {
        $this->content = $content;
        self::execute("UPDATE checklist_note_items SET content = :content WHERE id = :id", [
            "content" => $this->content,
            "id" => $this->id
        ]);
        $noteId = $this->checklistNoteId;
        $editionDate = new DateTime();
        self::execute("UPDATE notes SET edited_at = :editionDate WHERE id = :noteId", [
            "noteId" => $noteId,
            "editionDate" => $editionDate->format('Y-m-d H:i:s') 
        ]);
    }

    public static function validateItemContent(string $content) : array {
        $errors = [];
            if (strlen(Tools::sanitize($content)) < Configuration::get("item_min_length") || strlen(Tools::sanitize($content)) > Configuration::get("item_max_length")){
                $errors [] = "Item length must be between 1 and 60";
            }
        return $errors;
    }

}
