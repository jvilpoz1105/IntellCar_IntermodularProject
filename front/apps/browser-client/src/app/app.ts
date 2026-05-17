import { Component, inject, ElementRef, OnInit, signal } from '@angular/core';
import { Router, RouterOutlet, NavigationStart, NavigationEnd } from '@angular/router';
import { filter } from 'rxjs/operators';
import gsap from 'gsap';
import { NgxSonnerToaster } from 'ngx-sonner';

@Component({
	selector: 'app-root',
	standalone: true,
	imports: [RouterOutlet, NgxSonnerToaster],
	templateUrl: './app.html',
	styleUrl: './app.css',
})
export class App implements OnInit {
	private router = inject(Router);
	private el = inject(ElementRef);

	isPublishModalOpen = signal(false);
	isSocialModalOpen  = signal(false);
	isKddModalOpen     = signal(false);
	isGarageModalOpen  = signal(false);

	ngOnInit(): void {
		this.router.events.pipe(
			filter(e => e instanceof NavigationStart)
		).subscribe(() => {
			gsap.to(this.el.nativeElement, { opacity: 0, duration: 0.18, ease: 'power1.in' });
		});

		this.router.events.pipe(
			filter(e => e instanceof NavigationEnd)
		).subscribe(() => {
			gsap.fromTo(
				this.el.nativeElement,
				{ opacity: 0, y: 12 },
				{ opacity: 1, y: 0, duration: 0.28, ease: 'power2.out', clearProps: 'transform' }
			);
		});
	}

	togglePublishModal() { this.isPublishModalOpen.update(v => !v); }
	toggleSocialModal()  { this.isSocialModalOpen.update(v => !v); }
	toggleKddModal()     { this.isKddModalOpen.update(v => !v); }
	toggleGarageModal()  { this.isGarageModalOpen.update(v => !v); }
}

