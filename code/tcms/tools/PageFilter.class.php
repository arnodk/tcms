<?php
/**
 * Created by PhpStorm.
 * User: arno
 * Date: 28.10.2019
 * Time: 19:53
 */

namespace tcms\tools;


class PageFilter
{
    private $aData;
    private $aFilters;
    private $aConfig;
    private $iDefaultPageSize = 2;
    private $iPage = 1;

    public function __construct($aConfig=[])
    {
        $this->aConfig = $aConfig;
    }

    public function dataForPage()
    {
        $aResult = [];
        $aResult['data']=array_slice($this->aData,($this->iPage - 1) * $this->getPageSize(), $this->getPageSize());
        $aResult['is_first_page']=$this->isFirstPage();
        $aResult['is_last_page']=$this->isLastPage();
        $aResult['number_of_pages']=$this->getNumberOfPages();
        $aResult['total_items']=count($this->aData);
        $aResult['page']=$this->getPage();
        return $aResult;
    }

    public function setPage($iPage)
    {
        $this->iPage = intval($iPage);
    }

    public function getPage()
    {
        return $this->iPage;
    }

    public function isFirstPage()
    {
        return ($this->iPage === 1);
    }

    public function getNumberOfPages()
    {
        return ceil(count($this->aData) / $this->getPageSize());
    }

    public function isLastPage()
    {
        return ($this->iPage === intval($this->getNumberOfPages()));
    }

    public function setData($aData)
    {
        $this->aData = $aData;
    }

    public function addDataRow($aDataRow) {
        $this->aData[] = $aDataRow;
    }

    public function getData()
    {
        return $this->aData;
    }

    public function setFilters($aFilters)
    {
        $this->aFilters = $aFilters;
    }

    public function getFilters()
    {
        return $this->aFilters;
    }

    public function getPageSize()
    {
        return intval((!empty($this->aConfig['page_size'])) ? $this->aConfig['page_size'] : $this->iDefaultPageSize);
    }
}