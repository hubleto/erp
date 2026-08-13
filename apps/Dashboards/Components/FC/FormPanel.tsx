import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

export interface FormPanelProps extends FormProps {}

const componentName = 'FormPanel'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Dashboards';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormPanelProps) => {
  const form = React.useContext(FormMetaContext);

return <>
    <Input field='id_dashboard' />
    <Input field='board_url_slug' customInputProps={{
      cssClass: 'text-2xl',
      onChange: (input: any, value: any) => {
        const enumValues = input.props.enumValues;
        form.changeRecord({title: enumValues[value] ?? '-'})
      }
    }} />
    <Input field='title' />
    <Input field='width' />
    <Input field='configuration' />
  </>;
}

/** FormPanel */
const FormPanel = (props: FormPanelProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Panel'}
    urlSlug='dashboards/panels'
    endpointParams={{}}
    title={{field: 'title', sub: T.translate('Panel')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormPanel;
