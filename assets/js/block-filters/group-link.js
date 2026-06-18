import { addFilter } from '@wordpress/hooks';
import {
	BlockControls,
	InspectorControls,
	__experimentalLinkControl as LinkControl,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import {
	PanelBody,
	PanelRow,
	Popover,
	ToggleControl,
	ToolbarButton,
	ToolbarGroup,
} from '@wordpress/components';
import { createHigherOrderComponent } from '@wordpress/compose';
import { useSelect } from '@wordpress/data';
import { useState, useRef } from '@wordpress/element';
import { link, linkOff } from '@wordpress/icons';
import { __ } from '@wordpress/i18n';

const BLOCK_NAME = 'core/group';

addFilter('blocks.registerBlockType', 'linked-group-blocks/group-link-attributes', (settings, name) => {
	if (name !== BLOCK_NAME) {
		return settings;
	}
	return {
		...settings,
		attributes: {
			...settings.attributes,
			groupLinkUrl: {
				type: 'string',
				default: '',
			},
			groupLinkOpenInNewTab: {
				type: 'boolean',
				default: false,
			},
			groupLinkToCurrentPost: {
				type: 'boolean',
				default: false,
			},
		},
	};
});

const withGroupLinkControls = createHigherOrderComponent((BlockEdit) => {
	return (props) => {
		const { name, attributes, setAttributes, clientId } = props;

		if (name !== BLOCK_NAME) {
			return <BlockEdit {...props} />;
		}

		const { groupLinkUrl, groupLinkOpenInNewTab, groupLinkToCurrentPost } = attributes;
		const [isPopoverOpen, setIsPopoverOpen] = useState(false);
		const toolbarButtonRef = useRef(null);

		const isInQueryLoop = useSelect(
			(select) => {
				const { getBlockParents, getBlockName } = select(blockEditorStore);
				return getBlockParents(clientId).some(
					(id) => getBlockName(id) === 'core/post-template',
				);
			},
			[clientId],
		);

		const hasLink = groupLinkToCurrentPost || !!groupLinkUrl;

		const linkControlProps = {
			value: {
				url: groupLinkUrl,
				opensInNewTab: groupLinkOpenInNewTab,
			},
			onChange: ({ url = '', opensInNewTab = false } = {}) => {
				setAttributes({
					groupLinkUrl: url ?? '',
					groupLinkOpenInNewTab: opensInNewTab ?? false,
				});
			},
			onRemove: () => {
				setAttributes({
					groupLinkUrl: '',
					groupLinkOpenInNewTab: false,
				});
				setIsPopoverOpen(false);
			},
			hasTextControl: false,
			hasRichPreviews: false,
			settings: [
				{
					id: 'opensInNewTab',
					title: __('Open in new tab', 'linked-group-blocks'),
				},
			],
		};

		const linkPanelContent = (
			<>
				{isInQueryLoop && (
					<PanelRow>
						<ToggleControl
							label={__('Link to current post', 'linked-group-blocks')}
							checked={groupLinkToCurrentPost}
							onChange={(value) => {
								setAttributes({ groupLinkToCurrentPost: value });
							}}
						/>
					</PanelRow>
				)}
				{!groupLinkToCurrentPost && (
					<PanelRow>
						<LinkControl {...linkControlProps} />
					</PanelRow>
				)}
			</>
		);

		return (
			<>
				<BlockEdit {...props} />

				<BlockControls>
					<ToolbarGroup>
						<ToolbarButton
							ref={toolbarButtonRef}
							icon={hasLink ? linkOff : link}
							label={
								hasLink
									? __('Edit link', 'linked-group-blocks')
									: __('Add link', 'linked-group-blocks')
							}
							onClick={() => setIsPopoverOpen((open) => !open)}
							isActive={hasLink}
						/>
					</ToolbarGroup>
				</BlockControls>

				{isPopoverOpen && (
					<Popover
						anchor={toolbarButtonRef.current}
						onClose={() => setIsPopoverOpen(false)}
						placement="bottom-start"
						focusOnMount
					>
						<div style={{ padding: '12px 16px', whiteSpace: 'nowrap' }}>
							{linkPanelContent}
						</div>
					</Popover>
				)}

				<InspectorControls>
					<PanelBody
						title={__('Link', 'linked-group-blocks')}
						initialOpen={hasLink}
						icon={link}
						className="calhfa-group-link-panel"
					>
						{linkPanelContent}
					</PanelBody>
				</InspectorControls>
			</>
		);
	};
}, 'withGroupLinkControls');

addFilter('editor.BlockEdit', 'linked-group-blocks/group-link-controls', withGroupLinkControls);
