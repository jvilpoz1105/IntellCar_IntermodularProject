import { Component, Input, ElementRef, ViewChild, AfterViewInit, OnChanges, SimpleChanges } from '@angular/core';
import { CommonModule } from '@angular/common';
import { gsap } from 'gsap';

import { MediaStatus } from '../publish.types';

@Component({
  selector: 'app-media-item',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div 
      class="relative aspect-square rounded-xl overflow-hidden border-2 transition-all duration-500 bg-slate-900 group"
      [ngClass]="{
        'border-slate-800': status === 'pending' || status === 'uploading',
        'border-blue-500 shadow-[0_0_15px_rgba(59,130,246,0.5)]': status === 'analyzing',
        'border-emerald-500 shadow-[0_0_15px_rgba(16,185,129,0.3)]': status === 'success',
        'border-red-500 shadow-[0_0_15px_rgba(239,68,68,0.3)]': status === 'error'
      }"
    >
      <!-- Image Thumbnail -->
      @if (imageUrl) {
        <img [src]="imageUrl" class="w-full h-full object-cover" [alt]="'Upload preview'" />
      } @else {
        <div class="w-full h-full flex items-center justify-center bg-slate-950">
           <svg class="animate-spin h-6 w-6 text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </div>
      }

      <!-- Laser Scanner Overlay (GSAP Target) -->
      <div 
        #scanner 
        class="absolute top-0 left-0 w-full h-[2px] bg-gradient-to-r from-transparent via-blue-400 to-transparent shadow-[0_0_8px_#3b82f6] opacity-0 pointer-events-none z-10"
      ></div>

      <!-- Processing Overlay -->
      @if (status === 'uploading' || status === 'analyzing') {
        <div class="absolute inset-0 bg-slate-950/40 backdrop-blur-[1px] flex flex-col items-center justify-center gap-2">
            @if (status === 'uploading') {
              <div class="w-12 h-1 bg-slate-800 rounded-full overflow-hidden">
                <div class="h-full bg-blue-500 animate-[loading_1s_infinite]"></div>
              </div>
              <span class="text-[10px] font-bold text-white uppercase tracking-widest">Uploading</span>
            } @else {
               <span class="text-[10px] font-bold text-blue-400 animate-pulse uppercase tracking-widest">AI Scanning</span>
            }
        </div>
      }

      <!-- Status Badges -->
      <div class="absolute top-2 right-2 flex gap-1">
        @if (status === 'success') {
          <div class="p-1 bg-emerald-500 rounded-full text-white shadow-lg animate-in zoom-in">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
          </div>
        } @else if (status === 'error') {
          <div class="p-1 bg-red-500 rounded-full text-white shadow-lg animate-in zoom-in">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
          </div>
        }
      </div>

      <!-- Delete Button (Only on hover) -->
      <button 
        (click)="onDelete()"
        class="absolute bottom-2 right-2 p-1.5 bg-slate-900/80 hover:bg-red-500 text-white rounded-lg opacity-0 group-hover:opacity-100 transition-all duration-200 backdrop-blur-sm"
      >
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18m-2 0v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6m3 0V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
      </button>
    </div>
  `,
  styles: [`
    @keyframes loading {
      0% { transform: translateX(-100%); }
      100% { transform: translateX(100%); }
    }
  `]
})
export class MediaItemComponent implements AfterViewInit, OnChanges {
  @Input() imageUrl: string | null = null;
  @Input() status: MediaStatus = 'pending';
  @Input() deleteCallback?: () => void;

  @ViewChild('scanner') scanner!: ElementRef;
  private scannerTween?: gsap.core.Tween;

  ngAfterViewInit() {
    this.updateScannerState();
  }

  ngOnChanges(changes: SimpleChanges) {
    if (changes['status'] && this.scanner) {
      this.updateScannerState();
    }
  }

  private updateScannerState() {
    if (this.status === 'analyzing') {
      this.startScanner();
    } else {
      this.stopScanner();
    }
  }

  private startScanner() {
    if (this.scannerTween) return;
    
    gsap.set(this.scanner.nativeElement, { opacity: 1, top: '0%' });
    this.scannerTween = gsap.to(this.scanner.nativeElement, {
      top: '100%',
      duration: 1.5,
      repeat: -1,
      yoyo: false,
      ease: 'power1.inOut'
    });
  }

  private stopScanner() {
    if (this.scannerTween) {
      this.scannerTween.kill();
      this.scannerTween = undefined;
      gsap.to(this.scanner.nativeElement, { opacity: 0, duration: 0.3 });
    }
  }

  onDelete() {
    if (this.deleteCallback) {
      this.deleteCallback();
    }
  }
}
