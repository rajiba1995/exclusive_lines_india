<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\CommonPage;

class CommonPageEdit extends Component
{
    public $pageId,$content,$pageName;

    public function mount($id){
        $page = CommonPage::findOrFail($id);
        $this->pageId = $page->id;
        $this->content = $page->content ?? '';
        $this->pageName = $page->page_heading;
    }

    public function rules(){
        return [
            'content' => 'required'
        ];
    }

    public function messages(){
        return [
             'content.required' => 'Content is required'
        ];
    }

    public function update(){
        $this->validate();

        $page = CommonPage::findOrFail($this->pageId);
        $page->content = $this->content;
        $page->save();

        session()->flash('message', "Updated {$page->page_heading} Successfully");
        return redirect()->route('admin.common_pages.index');
    }

    public function render()
    {
         $this->dispatch('refreshCKEditor', content: '');
        return view('livewire.admin.common-page-edit');
    }
}
