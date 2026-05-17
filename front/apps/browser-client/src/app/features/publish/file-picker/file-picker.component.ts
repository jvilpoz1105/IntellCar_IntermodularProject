import { Component, EventEmitter, Output } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-file-picker',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="w-full">
      <input 
        #fileInput
        type="file" 
        multiple 
        accept="image/*" 
        (change)="onFilesSelected($event)" 
        class="hidden"
      />
      
      <button 
        (click)="fileInput.click()"
        class="w-full flex flex-col items-center justify-center gap-3 p-8 border-2 border-dashed border-slate-800 hover:border-slate-600 hover:bg-slate-900/50 rounded-xl transition-all duration-200 group"
      >
        <div class="p-3 bg-slate-900 rounded-full group-hover:scale-110 transition-transform duration-300 shadow-xl">
           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-500"><path d="M5 12h14m-7-7v14"/></svg>
        </div>
        <div class="text-center">
          <p class="text-sm font-semibold text-slate-200">Añadir imágenes</p>
          <p class="text-xs text-slate-500 mt-1">Arrastra archivos aquí o haz clic para explorar</p>
        </div>
      </button>
    </div>
  `,
  styles: [`:host { display: block; }`]
})
export class FilePickerComponent {
  @Output() filesSelected = new EventEmitter<FileList>();

  onFilesSelected(event: any) {
    const files = event.target.files as FileList;
    if (files.length > 0) {
      this.filesSelected.emit(files);
      // Reset input to allow selecting same files again if needed
      event.target.value = '';
    }
  }
}
