<?php

class NoteShare extends Model {
    private int $noteId;
    private int $userId;
    private bool $editor;

    public function __construct(int $userId, int $noteId, bool $editor) {
        $this->noteId = $noteId;
        $this->userId = $userId;
        $this->editor = $editor;
    }

    public function getNoteId() {
        return $this->noteId;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getEditor() {
        return $this->editor;
    }

    public static function getAllSharedNotes() : array {
        $query = self::execute("SELECT * FROM note_shares", []);
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $results[] = new NoteShare($row["note"], $row["user"], $row["editor"]);
        }
        return $results;
    }

    public static function isSharedNote($id) : NoteShare|bool {
        $query = self::execute("SELECT * FROM note_shares WHERE note = :id", ["id" => $id]);
        $data = $query->fetch();
        if ($query->rowCount() == 0) { 
            return false;
        }
        else {
            return new NoteShare( $data["user"], $data["note"], $data["editor"]);
        }
    }

    public function addShare() {
        $editorValue = $this->editor ? 1 : 0;
        self::execute("INSERT INTO note_shares(note, user, editor) VALUES(:noteId, :userId, :editor)", 
                        ["noteId" => $this->noteId, "userId" => $this->userId, "editor" => $editorValue]);
    }

    public function deleteShare() {
        $editorValue = $this->editor ? 1 : 0;
        self::execute("DELETE FROM note_shares WHERE note = :noteId AND user = :userId AND editor = :editor",
                        ["noteId" => $this->noteId, "userId" => $this->userId, "editor" => $editorValue]);
    }

    public function togglePermission() {
        //$newPermission = !$this->editor;
        $newPermission = !$this->editor ? 1 : 0;
        $editorValue = $this->editor ? 1 : 0;
        self::execute("UPDATE note_shares SET editor = :newPermission WHERE note = :noteId AND user = :userId AND editor = :editor",
        ["noteId" => $this->noteId, "userId" => $this->userId, "editor" => $editorValue, "newPermission" => $newPermission]);
    }
}