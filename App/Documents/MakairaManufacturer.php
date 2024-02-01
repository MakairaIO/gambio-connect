<?php

namespace GXModules\Makaira\GambioConnect\App\Documents;

use DateTime;

class MakairaManufacturer extends MakairaEntity
{
    private string $manufacturerTitle;
    private string $metaTitle;
    private string $metaDescription;
    private string $metaKeywords;
    private string $remoteUrl;
    private bool $isUrlClicked;

    private ?DateTime $createdAt = null;
    private ?DateTime $updatedAt = null;
    private ?DateTime $lastClickedAt = null;


    public function toArray(): array
    {
        return [
            /* MAKAIRA fields */
            ...parent::toArray(),

            /* Manufacturer fields */
            'manufacturer_title' => $this->manufacturerTitle,
            'meta_title' => $this->metaTitle,
            'meta_description' => $this->metaDescription,
            'meta_keywords' => $this->metaKeywords,
            'remote_url' => $this->remoteUrl,
            'is_url_clicked' => $this->isUrlClicked,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'last_clicked_at' => $this->lastClickedAt,
        ];
    }


    public function getManufacturerTitle(): string
    {
        return $this->manufacturerTitle;
    }


    public function setManufacturerTitle(string $manufacturerTitle): MakairaManufacturer
    {
        $this->manufacturerTitle = $manufacturerTitle;

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
