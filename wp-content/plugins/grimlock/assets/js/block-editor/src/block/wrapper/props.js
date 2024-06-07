import { useBlockProps } from '@wordpress/block-editor';
import { applyFilters } from '@wordpress/hooks';

/**
 * Generate the container block props
 *
 * @param attributes Block attributes
 * @param isSaveContext True when in save context
 * @return {*}
 */
export default ( attributes, isSaveContext = false ) => {
	let borderStyles = attributes.borders?.width ? {
		top: attributes.borders,
		bottom: attributes.borders,
		left: attributes.borders,
		right: attributes.borders,
	} : attributes.borders;
	
	let blockProps = applyFilters( 'grimlock.blocks.wrapper.blockProps', {
		className: '',
		style: {
			// Inline styles
			'--grimlock-wrapper-text-color': attributes?.textColor?.text || null,
			'--grimlock-wrapper-headings-color': attributes?.textColor?.headings || null,
			'--grimlock-wrapper-background-color': attributes.backgroundColor?.background || null,
			'--grimlock-wrapper-background-position': attributes.backgroundImage?.position ? `${ attributes.backgroundImage.position.x * 100 }% ${ attributes.backgroundImage.position.y * 100 }%` : null,
			'--grimlock-wrapper-background-image': attributes.backgroundImage?.url ? `url(${attributes.backgroundImage.url})` : null,
			'--grimlock-wrapper-background-overlay-color': attributes.backgroundColor?.overlay || null,
			'--grimlock-wrapper-margin-top': !! attributes.margin?.top && attributes.margin.top != 0 ? attributes.margin.top : null,
			'--grimlock-wrapper-margin-bottom': !! attributes.margin?.bottom && attributes.margin.bottom != 0 ? attributes.margin.bottom : null,
			'--grimlock-wrapper-padding-top': !! attributes.padding?.top && attributes.padding.top != 0 ? attributes.padding.top : null,
			'--grimlock-wrapper-padding-right': !! attributes.padding?.right && attributes.padding.right != 0 ? attributes.padding.right : null,
			'--grimlock-wrapper-padding-bottom': !! attributes.padding?.bottom && attributes.padding.bottom != 0 ? attributes.padding.bottom : null,
			'--grimlock-wrapper-padding-left': !! attributes.padding?.left && attributes.padding.left != 0 ? attributes.padding.left : null,
			'--grimlock-wrapper-border-top': !! borderStyles?.top?.width ? `${borderStyles.top.width} ${borderStyles.top.style} ${borderStyles.top.color}` : null,
			'--grimlock-wrapper-border-bottom': !! borderStyles?.bottom?.width ? `${borderStyles.bottom.width} ${borderStyles.bottom.style} ${borderStyles.bottom.color}` : null,
			'--grimlock-wrapper-border-left': !! borderStyles?.left?.width ? `${borderStyles.left.width} ${borderStyles.left.style} ${borderStyles.left.color}` : null,
			'--grimlock-wrapper-border-right': !! borderStyles?.right?.width ? `${borderStyles.right.width} ${borderStyles.right.style} ${borderStyles.right.color}` : null,
			'--grimlock-wrapper-border-top-left-radius': !! attributes.borderRadius?.topLeft && attributes.borderRadius.topLeft != 0 ? attributes.borderRadius.topLeft : null,
			'--grimlock-wrapper-border-top-right-radius': !! attributes.borderRadius?.topRight && attributes.borderRadius.topRight != 0 ? attributes.borderRadius.topRight : null,
			'--grimlock-wrapper-border-bottom-left-radius': !! attributes.borderRadius?.bottomLeft && attributes.borderRadius.bottomLeft != 0 ? attributes.borderRadius.bottomLeft : null,
			'--grimlock-wrapper-border-bottom-right-radius': !! attributes.borderRadius?.bottomRight && attributes.borderRadius.bottomRight != 0 ? attributes.borderRadius.bottomRight : null,
			'--grimlock-wrapper-z-index': !! attributes.zIndex && attributes.zIndex != 0 ? attributes.zIndex : null,
			'--grimlock-wrapper-overflow': ( !! attributes.overflow && attributes.overflow !== 'hidden' ) ? attributes.overflow : null,
		},
	}, attributes );

	if ( attributes.spread )
		blockProps.className += ` wp-block-grimlock-wrapper--inner-${attributes.spread}`;

	if ( ! isSaveContext )
		return useBlockProps( blockProps );
	else
		return useBlockProps.save( blockProps );
};