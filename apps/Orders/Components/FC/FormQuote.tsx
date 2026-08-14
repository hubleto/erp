import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import Divider from '@hubleto/react-ui/components/fc/FormComponents/Divider';

export interface FormQuoteProps extends FormProps {}

const componentName = 'FormQuote'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Orders';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormQuoteProps) => {
  return <div className='flex gap-2'>
    <div className='flex-1'>
      <Input field='id_order' />
      <Input field='version' />
      <Input field='summary' />
      <Input field='date_created' />
      <Input field='date_sent' />
      <Input field='id_approved_by' />
      <Input field='date_approved' />
    </div>
    <div className='flex-2'>
      {[1,2,3,4,5].map((i, key) => {
        return <div key={key}>
          <Divider>{T.translate('Document #') + i}</Divider>
          <div className='flex gap-2'>
            <div className='flex-3'>
              <Input field={'online_document_' + i} customInputProps={{wrapperCssClass: 'flex gap-2'}} />
              <Input field={'final_pdf_' + i} customInputProps={{wrapperCssClass: 'flex gap-2'}} />
            </div>
            <div className='flex-2'>
              <Input field={'notes_document_' + i} renderOnlyInputField customInputProps={{cssStyle: {height: '4.5em'}}} />
            </div>
          </div>
        </div>;
      })}
    </div>
  </div>;
}

/** FormQuote */
const FormQuote = (props: FormQuoteProps) => {
  return <Form
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Quote'}
    urlSlug='orders/quotes'
    endpointParams={{}}
    title={{fields: ['version', 'summary'], sub: T.translate('Quote')}}
    tabs={{default: {content: () => <TabDefault {...props} />}}}
    {...props}
  ></Form>;
}

export default FormQuote;
