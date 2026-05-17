import { Component, inject, ElementRef, OnInit } from '@angular/core';
import { Router, RouterOutlet, NavigationStart, NavigationEnd } from '@angular/router';
import { filter } from 'rxjs/operators';
import gsap from 'gsap';
import { NgxSonnerToaster } from 'ngx-sonner';
import { ToastContainerComponent } from './core/components/toast-container/toast-container.component';

@Component({
	selector: 'app-root',
	standalone: true,
	imports: [RouterOutlet, NgxSonnerToaster, ToastContainerComponent],
	templateUrl: './app.html',
	styleUrl: './app.css',
})
export class App implements OnInit {
	private router = inject(Router);
	private el = inject(ElementRef);

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
}

