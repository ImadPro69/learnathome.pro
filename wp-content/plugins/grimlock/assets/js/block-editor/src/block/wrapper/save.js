import { InnerBlocks } from '@wordpress/block-editor';

export default ( { blockProps } ) => {
	return (
		<div { ...blockProps }>
			<div className="wp-block-grimlock-wrapper__bg" />
			<div className="wp-block-grimlock-wrapper__bg-overlay" />

			<div className="wp-block-grimlock-wrapper__inner">
				<InnerBlocks.Content />
			</div>
		</div>
	);
};