<?php
class Manojsingh_Productquestions_Block_Adminhtml_Productquestions_Grid_Column_Rating extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        if($row->getVoteYes()){$yes = $row->getVoteYes();}else{$yes = 0;}
        if($row->getVoteNo()){$no = $row->getVoteNo();}else{$no = 0;}
        if($row->getReport()){$rep = $row->getReport();}else{$rep = 0;}
        return sprintf('%s&nbsp;/&nbsp;%s&nbsp;/&nbsp;%s', $yes, $no, $rep);
    }

}
