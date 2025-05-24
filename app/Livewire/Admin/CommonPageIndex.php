<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\CommonPage;

class CommonPageIndex extends Component
{
    public $common_pages;
    public $search ='';
    
      

  

    public function toggleStatus($id)
    {
        $page = CommonPage::findOrFail($id);
        $page->status = !$page->status;
        $page->save();
        session()->flash('message', 'Status updated successfully.');

    }
    
    public function render()
    {
        $this->common_pages = CommonPage::when($this->search, function ($query) {
        $query->where('page_heading', 'like', '%' . $this->search . '%');
    })->get();
        return view('livewire.admin.common-page-index');
    }
}
