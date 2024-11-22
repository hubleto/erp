import React, { Component } from 'react';
import Form, { FormProps, FormState } from 'adios/Form';
import InputTags2 from 'adios/Inputs/Tags2';
import FormInput from 'adios/FormInput';
import { getUrlParam } from 'adios/Helper';

export interface FormDocumentProps extends FormProps {
  creatingForModel?: string,
  creatingForId?: number,
}

export interface FormDocumentState extends FormState {}

export default class FormDocument<P, S> extends Form<FormDocumentProps,FormDocumentState> {
  static defaultProps: any = {
    ...Form.defaultProps,
    model: 'CeremonyCrmApp/Modules/Core/Documents/Models/Document',
  };

  props: FormDocumentProps;
  state: FormDocumentState;

  constructor(props: FormDocumentProps) {
    super(props);
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: FormDocumentProps) {
    return {
      ...super.getStateFromProps(props),
    };
  }

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
            {'New Document'}
          </h2>
        </>
      );
    } else {
      return (
        <>
          <h2>
            {this.state.record.name
              ?
              <div className='flex flex-col justify-center'>
                <span>{this.state.record.name}</span>
                <span className='text-xs text-gray-400 font-normal'>Document</span>
              </div>
              :
              <div className='flex flex-col justify-center'>
                <span>'[Undefined Name]'</span>
                <span className='text-xs text-gray-400 font-normal'>Document</span>
              </div>
              }
          </h2>
        </>
      );
    }
  }

  renderContent(): JSX.Element {
    const R = this.state.record;

    const showAdditional: boolean = R.id > 0 ? true : false;
    var entryURL: string = "";
    var entryType: string = "";

    if (R.COMPANY_DOCUMENT && !this.props.creatingForModel) {
      entryURL = "customers/companies?recordId="+R.COMPANY_DOCUMENT.id_company;
      entryType = "Company"
    } else if (R.LEAD_DOCUMENT && !this.props.creatingForModel) {
      entryURL = "sales/leads?recordId="+R.LEAD_DOCUMENT.id_lead;
      entryType = "Lead"
    } else if (R.DEAL_DOCUMENT && !this.props.creatingForModel) {
      entryURL = "sales/deals?recordId="+R.DEAL_DOCUMENT.id_deal;
      entryType = "Deal"
    }

    return (
      <>
        <div className='card mt-4'>
          <div className='card-body flex flex-row gap-2'>
              {this.inputWrapper('name')}
              {this.inputWrapper('file')}
              {!this.props.creatingForModel && showAdditional && entryType ?
                <a href={entryURL} className='btn btn-primary mt-2'>
                  <span className='icon'><i className='fas fa-eye'></i></span>
                  <span className='text'>Go to {entryType}</span>
                </a>
              : null}
          </div>
        </div>
      </>
    );
  }

}
