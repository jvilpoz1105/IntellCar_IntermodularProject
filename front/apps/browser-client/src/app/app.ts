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
import { PublishModalComponent } from './features/publish/publish-modal/publish-modal.component';
import { SocialModalComponent } from './features/publish/social-modal/social-modal.component';
import { KddModalComponent } from './features/publish/kdd-modal/kdd-modal.component';
import { GarageModalComponent } from './features/publish/garage-modal/garage-modal.component';
import { CommonModule } from '@angular/common';

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
		PublishModalComponent,
		SocialModalComponent,
		KddModalComponent,
		GarageModalComponent,
		CommonModule
	],
	templateUrl: './app.html',
	styleUrl: './app.css',
})
export class App {
	protected readonly title = signal('browser-client');

	// Señales de visibilidad para cada modal
	public readonly isPublishModalOpen = signal(false);
	public readonly isSocialModalOpen  = signal(false);
	public readonly isKddModalOpen     = signal(false);
	public readonly isGarageModalOpen  = signal(false);

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

	togglePublishModal() { this.isPublishModalOpen.update(v => !v); }
	toggleSocialModal()  { this.isSocialModalOpen.update(v => !v); }
	toggleKddModal()     { this.isKddModalOpen.update(v => !v); }
	toggleGarageModal()  { this.isGarageModalOpen.update(v => !v); }
}
