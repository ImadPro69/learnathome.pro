import { __ } from '@wordpress/i18n';

export default [
	{
		name: 'one-column',
		title: __( 'One Column Section', 'grimlock' ),
		icon: 'align-center',
		scope: 'block',
		attributes: { margin: { top: '4%', bottom: '4%' }, padding: { top: '4%', bottom: '4%' }, align: 'full' },
		innerBlocks: [
			[ 'core/image', { align: 'center' } ],
			[ 'core/heading', { placeholder: __( 'Title...', 'grimlock' ) } ],
			[ 'core/heading', { placeholder: __( 'Subtitle...', 'grimlock' ), fontSize: 'subheading' } ],
			[ 'core/paragraph', { placeholder: __( 'Text...', 'grimlock' ) } ],
			[ 'core/buttons' ],
		],
	},
	{
		name: 'two-columns-left',
		title: __( 'Two Columns Text Left', 'grimlock' ),
		icon: 'align-right',
		scope: 'block',
		attributes: { margin: { top: '4%', bottom: '4%' }, padding: { top: '4%', bottom: '4%' }, align: 'full' },
		innerBlocks: [
			[ 'core/columns', {}, [
				[ 'core/column', { width: '50%', verticalAlignment: 'center' }, [
					[ 'core/heading', { placeholder: __( 'Title...', 'grimlock' ) } ],
					[ 'core/heading', { placeholder: __( 'Subtitle...', 'grimlock' ), fontSize: 'subheading' } ],
					[ 'core/paragraph', { placeholder: __( 'Text...', 'grimlock' ) } ],
					[ 'core/buttons' ],
				] ],
				[ 'core/column', { width: '50%', verticalAlignment: 'center' }, [
					[ 'core/image', { align: 'center' } ],
				] ],
			] ],
		],
	},
	{
		name: 'two-columns-right',
		title: __( 'Two Columns Text Right', 'grimlock' ),
		icon: 'align-left',
		scope: 'block',
		attributes: { margin: { top: '4%', bottom: '4%' }, padding: { top: '4%', bottom: '4%' }, align: 'full' },
		innerBlocks: [
			[ 'core/columns', {}, [
				[ 'core/column', { width: '50%', verticalAlignment: 'center' }, [
					[ 'core/image', { align: 'center' } ],
				] ],
				[ 'core/column', { width: '50%', verticalAlignment: 'center' }, [
					[ 'core/heading', { placeholder: __( 'Title...', 'grimlock' ) } ],
					[ 'core/heading', { placeholder: __( 'Subtitle...', 'grimlock' ), fontSize: 'subheading' } ],
					[ 'core/paragraph', { placeholder: __( 'Text...', 'grimlock' ) } ],
					[ 'core/buttons' ],
				] ],
			] ],
		],
	},
];