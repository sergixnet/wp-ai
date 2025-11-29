import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import './editor.css';
import './style.css';

registerBlockType('citas-humor/cita-block', {
  title: __('Cita de Humor', 'citas-humor'),
  description: __('Muestra una cita humorística aleatoria', 'citas-humor'),
  category: 'widgets',
  icon: 'format-quote',
  attributes: {
    theme: {
      type: 'string',
      default: 'current',
    },
    className: {
      type: 'string',
      default: 'cita-humor-box',
    },
  },

  edit: ({ attributes, setAttributes }) => {
    const blockProps = useBlockProps();

    // Temas disponibles
    const themes = [
      { label: 'Usar tema actual del sitio', value: 'current' },
      { label: 'Gradiente Moderno', value: 'gradient' },
      { label: 'Clásico', value: 'classic' },
      { label: 'Minimalista', value: 'minimal' },
      { label: 'Oscuro', value: 'dark' },
      { label: 'Retro', value: 'retro' },
    ];

    // Descripción del tema seleccionado
    const themeDescriptions = {
      current: 'Usa el tema configurado en Ajustes → Citas de Humor',
      gradient: 'Diseño vibrante con gradiente morado y violeta',
      classic: 'Estilo elegante con tipografía serif',
      minimal: 'Diseño limpio y simple',
      dark: 'Tema oscuro con texto verde neón',
      retro: 'Estilo vintage años 80 con colores llamativos',
    };

    return (
      <>
        <InspectorControls>
          <PanelBody
            title={__('Configuración del Bloque', 'citas-humor')}
            initialOpen={true}
          >
            <SelectControl
              label={__('Tema Visual', 'citas-humor')}
              value={attributes.theme}
              options={themes}
              onChange={(theme) => setAttributes({ theme })}
              help={themeDescriptions[attributes.theme]}
            />

            <SelectControl
              label={__('Clase CSS personalizada', 'citas-humor')}
              value={attributes.className}
              options={[
                { label: 'Por defecto', value: 'cita-humor-box' },
                { label: 'Personalizada', value: 'custom' },
              ]}
              onChange={(className) => setAttributes({ className })}
            />
          </PanelBody>
        </InspectorControls>

        <div {...blockProps}>
          <div className="citas-humor-block-editor">
            <div className="citas-humor-block-header">
              <span className="dashicons dashicons-format-quote"></span>
              <h4>Cita de Humor</h4>
            </div>
            <p className="citas-humor-block-description">
              Este bloque mostrará una cita humorística aleatoria con el tema{' '}
              <strong>
                {themes.find((t) => t.value === attributes.theme)?.label}
              </strong>
              .
            </p>

            <div className="citas-humor-preview">
              <div
                className={attributes.className}
                data-theme-preview={
                  attributes.theme !== 'current' ? attributes.theme : null
                }
              >
                <p className="cita-humor-texto">
                  "Vista previa del bloque. En el frontend se mostrará una cita
                  aleatoria."
                </p>
              </div>
            </div>
          </div>
        </div>
      </>
    );
  },

  save: ({ attributes }) => {
    const blockProps = useBlockProps.save();

    return (
      <div {...blockProps}>
        <div
          className={attributes.className}
          data-cita-humor="block"
          data-theme={attributes.theme}
        >
          {/* El contenido se renderizará en PHP */}
        </div>
      </div>
    );
  },
});
