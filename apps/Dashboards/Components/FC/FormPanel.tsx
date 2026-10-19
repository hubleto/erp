import React from 'react';
import Translator from '@hubleto/react-ui/core/Translator';
import { type FormProps } from '@hubleto/react-ui/components/fc/FormInterfaces';
import Form, { FormMetaContext } from '@hubleto/react-ui/components/fc/Form';
import Input from '@hubleto/react-ui/components/fc/FormComponents/Input';
import { useRecordField } from '@hubleto/react-ui/components/fc/FormRecordStore';
import request from "@hubleto/react-ui/core/Request";

export interface FormPanelProps extends FormProps {}

const componentName = 'FormPanel'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Dashboards';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

/** TabDefault */
const TabDefault = (props: FormPanelProps) => {
  const form = React.useContext(FormMetaContext);
  const boardUrlSlug = useRecordField('board_url_slug');

  const [boardDescription, setBoardDescription] = React.useState(null);

  React.useEffect(() => {
    request.post(
      'dashboards/api/describe-board',
      { urlSlug: boardUrlSlug },
      {},
      (description: any) => {
        setBoardDescription(description);
      },
      (err: any) => {
        setBoardDescription(err);
      }
    );
  }, [boardUrlSlug]);

  return <div className='flex flex-col gap-2'>
    <div className='flex-dyn gap-2'>
      <div className='flex-4'>
        <Input field='title' customInputProps={{cssClass: 'text-[2em] border border-primary p-1 shadow rounded'}} />
      </div>
      <div className='flex-1'>
        <Input field='width' />
      </div>
    </div>
    <div className='flex-dyn gap-2'>
      <div className='grow flex-1'>
        <Input field='board_url_slug' customInputProps={{
          uiStyle: 'buttons-vertical',
          cssClass: 'text-primary',
          onChange: (input: any, value: any) => {
            const enumValues = input.enumValues;
            form.changeRecord({
              board_url_slug: value,
              title: enumValues[value]
            })
          }
        }} />
      </div>
      <div className='grow flex-1'>
        <div className='flex flex-col gap-2'>
          {boardDescription ?
            boardDescription.status == 'error' ?
              <div className='alert alert-danger'>
                Board cannot be configured.<br/>
                <br/>
                {boardDescription.message}
              </div>
            : <>
              <Input field='configuration' />
              <table className='table-default dense'>
                <thead>
                  <tr>
                    <th>Option</th>
                    <th>Type</th>
                    <th>Default</th>
                    <th>Description</th>
                  </tr>
                </thead>
                <tbody>
                  {boardDescription.options ? boardDescription.options.map((option) => {
                    return <tr>
                      <td>{option.variable}</td>
                      <td><div className='badge'>{option.type}</div></td>
                      <td>{option.default}</td>
                      <td>{option.description}</td>
                    </tr>
                  }) : null}
                </tbody>
              </table>
            </>
          : null}
        </div>
      </div>
    </div>
  </div>;
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
