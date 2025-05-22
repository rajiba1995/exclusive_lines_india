<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\CommonPage;

class CommonPageIndex extends Component
{
    public $common_pages;
    public $showForm = false;
    public $pageId,$title,$content;
    public $search ='';
    
        public function edit($id)
    {
        $page = CommonPage::findOrFail($id);
        $this->pageId = $id;
        $this->title = $page->page_heading;
        $this->content = $page->content;
    }

    public function update()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $page = CommonPage::findOrFail($this->pageId);
        $page->page_heading = $this->title;
        $page->content = $this->content;
        $page->save();

        session()->flash('message', 'Page updated successfully.');


        // Reset the form
        $this->resetForm();
    }

    public function toggleStatus($id)
    {
        $page = CommonPage::findOrFail($id);
        $page->status = !$page->status;
        $page->save();
        session()->flash('message', 'Status updated successfully.');

    }

   

    public function resetForm()
    {
        $this->reset(['pageId', 'title', 'content']);
        $this->dispatch('reset-ckeditor');
    }
    
    public function render()
    {
        $this->common_pages = CommonPage::when($this->search, function ($query) {
        $query->where('page_heading', 'like', '%' . $this->search . '%');
    })->get();
        return view('livewire.admin.common-page-index');
    }
}
