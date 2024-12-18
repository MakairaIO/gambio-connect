<?php

namespace GXModules\MakairaIO\MakairaConnect\App\Documents;

class MakairaEntity
{
    public const DOC_TYPE_PRODUCT = 'product';

    public const DOC_TYPE_VARIANT = 'variant';

    public const DOC_TYPE_CATEGORY = 'category';

    public const DOC_TYPE_MANUFACTURER = 'manufacturer';

    private string $id;

    private string $type;

    private int $shop = 1;

    private bool $active = true;

    private bool $searchable = true;

    private bool $hidden = false;

    protected bool $delete = false;

    private string $pictureUrlMain = '';

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'shop' => $this->shop,
            'active' => $this->active,
            'searchable' => $this->searchable,
            'hidden' => $this->hidden,
            'picture_url_main' => $this->pictureUrlMain,
            'delete' => $this->delete,
        ];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): MakairaEntity
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): MakairaEntity
    {
        $this->type = $type;

        return $this;
    }

    public function getShop(): int
    {
        return $this->shop;
    }

    public function setShop(int $shop): MakairaEntity
    {
        $this->shop = $shop;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): MakairaEntity
    {
        $this->active = $active;

        return $this;
    }

    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    public function setSearchable(bool $searchable): MakairaEntity
    {
        $this->searchable = $searchable;

        return $this;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden): MakairaEntity
    {
        $this->hidden = $hidden;

        return $this;
    }

    public function getPictureUrlMain(): string
    {
        return $this->pictureUrlMain;
    }

    public function setPictureUrlMain(string $pictureUrlMain): MakairaEntity
    {
        $this->pictureUrlMain = $pictureUrlMain;

        return $this;
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

    public function delete(): MakairaEntity
    {
        $this->delete = true;

        return $this;
    }
}
