import React, { Component } from 'react';
import { deepObjectMerge, getUrlParam } from 'adios/Helper';
import Form, { FormProps, FormState } from 'adios/Form';
import InputTags2 from 'adios/Inputs/Tags2';
import InputTable from 'adios/Inputs/Table';
import FormInput from 'adios/FormInput';

interface FormLeadProps extends FormProps {}

interface FormLeadState extends FormState {}

export default class FormLead<P, S> extends Form<FormLeadProps,FormLeadState> {
  static defaultProps: any = {
    ...Form.defaultProps,
    model: 'CeremonyCrmApp/Modules/Sales/Sales/Models/Lead',
  };

  props: FormLeadProps;
  state: FormLeadState;

  constructor(props: FormLeadProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormLeadProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

  /* normalizeRecord(record) {

    return record;
  } */

  renderHeaderLeft(): JSX.Element {
    return <>
      {this.state.isInlineEditing ? this.renderSaveButton() : this.renderEditButton()}
    </>;
  }

  renderHeaderRight(): JSX.Element {
    return <>
      {this.state.isInlineEditing ? this.renderDeleteButton() : null}
      {this.props.showInModal ? this.renderCloseButton() : null}
    </>;
  }

  renderTitle(): JSX.Element {
    if (getUrlParam('recordId') == -1) {
      return(
        <>
          <h2>
            {'New Lead'}
          </h2>
        </>
      );
    } else {
      return (
        <>
          <h2>
            {this.state.record.title
              ? this.state.record.title
              : '[Undefined Lead Name]'}
          </h2>
        </>
      );
    }
  }

  renderContent(): JSX.Element {
    const R = this.state.record;
    const showAdditional = R.id > 0 ? true : false;

    return (
      <>
        <div className='grid grid-cols-2 gap-1' style=
          {{gridTemplateAreas:`
            'info info'
            'history history'
            'button button'
          `}}>
            <div className='card mt-2' style={{gridArea: 'info'}}>
              <div className='card-header'>Lead Information</div>
              <div className='card-body flex flex-row gap-2'>
                <div className='grow'>
                  {this.inputWrapper('title')}
                  {this.inputWrapper('id_company')}
                  {this.inputWrapper('id_person')}
                  <div className='flex flex-row'>
                    {this.inputWrapper('price')}
                    {this.inputWrapper('id_currency')}
                  </div>
                  {showAdditional ? this.inputWrapper('id_status') : null}
                </div>
                <div className='border-l border-gray-200'></div>
                <div className='grow'>
                  {this.inputWrapper('id_user')}
                  {this.inputWrapper('date_close_expected')}
                  {this.inputWrapper('source_channel')}
                  {this.inputWrapper('note')}
                  <FormInput title='Labels'>
                    <InputTags2 {...this.getDefaultInputProps()}
                      value={this.state.record.LABELS}
                      model='CeremonyCrmApp/Modules/Core/Settings/Models/Label'
                      targetColumn='id_lead'
                      sourceColumn='id_label'
                      colorColumn='color'
                      onChange={(value: any) => {
                        this.updateRecord({LABELS: value});
                      }}
                    ></InputTags2>
                  </FormInput>
                  {showAdditional ? this.inputWrapper('is_archived') : null}
                </div>
              </div>
            </div>
            {showAdditional ?
              <div className='card mt-2' style={{gridArea: 'history'}}>
                <div className='card-header'>Lead History</div>
                <div className='card-body min-h-[100px] flex justify-center'>
                  {R.LEAD_HISTORY.length > 0 ?
                    R.LEAD_HISTORY.map((history, key) => (
                      <div className='w-full flex flex-row' style={{justifyContent: "space-around"}}>
                        <p className='font-bold self-center text-sm'>{history.description}</p><hr style={{width: "25%", alignSelf: "center"}}/><p className='self-center text-sm'>{history.change_date}</p>
                      </div>
                    ))
                    :
                    <p className='text-gray-400'>Lead has no history</p>
                  }
                </div>
              </div>
            : null}
            {showAdditional ?
              <div className='w-full flex flex-row justify-center' style={{gridArea: 'button'}}>
                <a className='btn btn-primary text-center p-3' onClick={()=>{}}>
                  <span className='icon'><i className='fas fa-forward'></i></span>
                  <span className='text'>Convert to a Deal</span>
                </a>
              </div>
            : null}
        </div>
      </>
    );
  }
}