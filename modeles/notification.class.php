<?php


class Notification {

    private ?int $idNotification;
    private ?string $contenu;
    private ?DateTime  $date;

    public function __construct(int $idNotification = null, ?string $contenu = null, ?DateTime $date = null) {
        $this->idNotification = $idNotification;
        $this->contenu = $contenu;
        $this->date = $date;
    }



    public function getIdNotification(): ?int {
        return $this->idNotification;
    }
    public function getcontenu(): ?string {
        return $this->contenu;
    }
    public function getDate(): ?DateTime {
        return $this->date;
    }

    public function setIdNotification(int $idNotification): void {
        $this->idNotification = $idNotification;
    }

    public function setcontenu(string $contenu): void {
        $this->contenu = $contenu;
    }

    public function setDate(DateTime $date): void {
        $this->date = $date;
    }





}