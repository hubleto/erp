import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormMeta, FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import Divider from '@hubleto/react-ui/components/fc/FormComponents/Divider';
import TablePanels from './TablePanels';

export interface FormDashboardProps extends FormProps {}

const componentName = 'FormDashboard'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Dashboards';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormDashboardProps) => {
  const form = React.useContext(FormMetaContext);

  const slugify = (text: string) => {
    if (text == null || text.length < 1) return "";
    return text
      .toString()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .toLowerCase()
      .trim()
      .replace(/[^a-z0-9-]/g, '-')
      .replace(/--+/g, '-')
      .replace(/^-+/, '')
      .replace(/-+$/, '');
  }


  return <>
    <div className='card'>
      <div className='card-body'>
        <Input field='title' customInputProps={{
          onChange: (input: any, newValue: any) => {
            form.changeField(input, slugify(newValue));
          }
        }} />
        <Input field='is_default' customInputProps={{yesText: 'Show on home screen'}} />
        <Input field='slug' />
      </div>
    </div>
    <Divider>{T.translate('Panels')}</Divider>
    {props.id < 0 ?
      <div className="badge badge-info">{T.translate('First create the dashboard, then you will be prompted to add panels.')}</div>
    :
      <div className='mt-2'>
        <TablePanels
          uid='dashboard_panels'
          idDashboard={props.id}
          parentForm={form}
        ></TablePanels>
      </div>
    }
  </>;
}

/** FormDashboard */
const FormDashboard = (props: FormDashboardProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Dashboard'}
    urlSlug='parent-app-slug/same-url-slug-as-in-table'
    endpointParams={{}}
    // onAfterFormInitialized={(form: any) => {}}
    // renderTitle={(): React.JSX.Element => { return <></>; }
    renderHeaderLeft={(form: FormMeta): React.JSX.Element => {
      const slug = useRecordField('slug');
      return <>
        {form.renderDefaultHeaderLeft()}
        <a
          className='btn btn-transparent btn-square'
          target='_blank'
          href={globalThis.hubleto.config.projectUrl + "/dashboards/~/" + slug}
        >
          <span className='icon'><i className='fas fa-eye'></i></span>
          <span className='text'>{T.translate("Preview")}</span>
        </a>
      </>
    }}

    title={{field: 'title', sub: T.translate('Dashboard')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormDashboard;