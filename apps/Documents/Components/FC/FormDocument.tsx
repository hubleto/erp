import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { type FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { type FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';

import TableVersions from './TableVersions';
import TableReviews from './TableReviews';

export interface FormDocumentProps extends FormProps {}

const componentName = 'FormDocument'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Documents';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormDocumentProps) => {
  const form = React.useContext(FormMetaContext);

  return <div className='flex gap-2'>
    <div className='flex-2'>
      <Input field='uid' customInputProps={{readonly: true}} />
      <div className='flex gap-2'>
        <Input field='model' customInputProps={{readonly: true}} />
        <Input field='record_id' customInputProps={{readonly: true}} />
      </div>
      <div className='flex gap-2'>
        <Input field='id_created_by' customInputProps={{readonly: true}} />
        <Input field='created_on' customInputProps={{readonly: true}} />
      </div>
      <Input field='name' customInputProps={{cssClass: 'text-2xl'}} />
      <div className='card'>
        <div className='card-header'>{T.translate('Versions')}</div>
        <div className='card-body'>
          {form.id > 0 ?
            <TableVersions
              tag={"table_documents_versions"}
              parentForm={form}
              // readonly={true}
              uid={props.uid + "_table_documents_versions"}
              idDocument={form.id}
            />
          :
            <div className="badge badge-info">{T.translate('First create the document to add document versions.')}</div>
          }
        </div>
      </div>
    </div>
    <div className='card flex-1'>
        <div className='card-header'>{T.translate('Reviews')}</div>
        <div className='card-body'>
          {form.id > 0 ?
            <TableReviews
              tag={"table_documents_reviews"}
              parentForm={form}
              uid={props.uid + "_table_documents_reviews"}
              idDocument={form.id}
            />
          :
            <div className="badge badge-info">{T.translate('First create the document to add document reviews.')}</div>
          }
        </div>
    </div>
  </div>
}

/** FormDocument */
const FormDocument = (props: FormDocumentProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Document'}
    urlSlug='documents'
    endpointParams={{}}
    onAfterFormInitialized={(form: any) => {}}
    title={{field: 'name', sub: T.translate('Document')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormDocument;
