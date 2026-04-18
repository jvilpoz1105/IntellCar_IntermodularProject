import { Component, signal, afterNextRender } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { HlmButton } from '@spartan-ng/helm/button';
import { HlmBadge } from '@spartan-ng/helm/badge';
import {
	HlmCard,
	HlmCardContent,
	HlmCardDescription,
	HlmCardFooter,
	HlmCardHeader,
	HlmCardTitle,
} from '@spartan-ng/helm/card';
import { animate, spring } from 'motion';
import { gsap } from 'gsap';

@Component({
	selector: 'app-root',
	standalone: true,
	imports: [
		RouterOutlet,
		HlmButton,
		HlmBadge,
		HlmCard,
		HlmCardContent,
		HlmCardDescription,
		HlmCardFooter,
		HlmCardHeader,
		HlmCardTitle,
	],
	templateUrl: './app.html',
	styleUrl: './app.css',
})
export class App {
	protected readonly title = signal('browser-client');

	constructor() {
		afterNextRender(() => {
			// Motion: Entrada suave de la tarjeta principal
			animate(
				'#welcome-card',
				{ 
					opacity: 1, 
					y: 0,
					scale: 1 
				},
				{ 
					duration: 0.8
				}
			);

			// GSAP: Efecto de flotación para el badge de "En Desarrollo"
			gsap.to('#dev-badge', {
				y: -6,
				duration: 2,
				repeat: -1,
				yoyo: true,
				ease: 'power1.inOut',
			});

			// Animación extra: Los botones aparecen con un pequeño retraso
			animate(
				'button',
				{ opacity: 1, y: 0 },
				{ delay: 0.5, duration: 0.5 }
			);
		});
	}
}
