<?php

namespace GXModules\MakairaIO\MakairaConnect\App\Documents;

use DateTime;

class MakairaManufacturer extends MakairaEntity
{
    private string $title = '';

    private string $metaTitle = '';

    private string $metaDescription = '';

    private string $metaKeywords = '';

    private string $remoteUrl = '';

    private bool $isUrlClicked = false;

    private ?DateTime $createdAt = null;

    private ?DateTime $updatedAt = null;

    private ?DateTime $lastClickedAt = null;

    public function toArray(): array
    {
        if($this->delete) {
            return [
                'delete' => $this->delete
            ];
        }

        return [
            /* MAKAIRA fields */
            ...parent::toArray(),

            /* Manufacturer fields */
            'title' => $this->title,
            'metaTitle' => $this->metaTitle,
            'metaDescription' => $this->metaDescription,
            'metaKeywords' => $this->metaKeywords,
            'remoteUrl' => $this->remoteUrl,
            'isUrlClicked' => $this->isUrlClicked,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updatedAt->format('Y-m-d H:i:s'),
            'lastClickedAt' => $this->lastClickedAt,
        ];
    }

    public function isDelete(): bool
    {
        return $this->delete;
    }

    public function setDelete(bool $delete): static
    {
        $this->delete = $delete;
        return $this;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }



    public function getMetaTitle(): string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(string $metaTitle): MakairaManufacturer
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    public function getMetaDescription(): string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(string $metaDescription): MakairaManufacturer
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    public function getMetaKeywords(): string
    {
        return $this->metaKeywords;
    }

    public function setMetaKeywords(string $metaKeywords): MakairaManufacturer
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    public function getRemoteUrl(): string
    {
        return $this->remoteUrl;
    }

    public function setRemoteUrl(string $remoteUrl): MakairaManufacturer
    {
        $this->remoteUrl = $remoteUrl;

        return $this;
    }

    public function isUrlClicked(): bool
    {
        return $this->isUrlClicked;
    }

    public function setIsUrlClicked(bool $isUrlClicked): MakairaManufacturer
    {
        $this->isUrlClicked = $isUrlClicked;

        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTime $createdAt): MakairaManufacturer
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTime $updatedAt): MakairaManufacturer
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getLastClickedAt(): ?DateTime
    {
        return $this->lastClickedAt;
    }

    public function setLastClickedAt(?DateTime $lastClickedAt): MakairaManufacturer
    {
        $this->lastClickedAt = $lastClickedAt;

        return $this;
    }
}
