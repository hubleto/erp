import React, { Component } from 'react';
import FormDeal, { FormDealProps, FormDealState } from '@hubleto/apps/community/Deals/Components/FormDeal'
import TranslatedComponent from "adios/TranslatedComponent";
import request from 'adios/Request';

interface P {
  form: FormDeal<FormDealProps, FormDealState>
}

interface S { }

export default class FormDealTopMenu extends TranslatedComponent<P, S> {
  props: P;
  state: S;

  translationContext: string = 'HubletoApp\\Community\\Projects\\Loader::Components\\FormProject';

  convertToProject(idDeal: number) {
    request.get(
      'projects/api/convert-deal-to-project',
      {idDeal: idDeal},
      (data: any) => {
        if (data.status == "success") {
          window.open(globalThis.main.config.rootUrl + `/projects/${data.idProject}`)
        }
      }
    );
  }

  confirmConvertToProject(idDeal: number) {
    globalThis.main.showDialogDanger(
      'Are you sure you want to convert this deal to a project?',
      {
        headerClassName: "dialog-warning-header",
        header: "Convert to project",
        footer: <>
          <button
            className="btn btn-yellow"
            onClick={() => {this.convertToProject(idDeal)}}
          >
            <span className="icon"><i className="fas fa-forward"></i></span>
            <span className="text">Yes, convert to project</span>
          </button>
          <button
            className="btn btn-transparent"
            onClick={() => { globalThis.main.lastShownDialogRef.current.hide(); }}
          >
            <span className="icon"><i className="fas fa-times"></i></span>
            <span className="text">No, do not convert</span>
          </button>
        </>
      }
    );
  }

  render() {
    const form = this.props.form;
    const R = form.state.record;

    return (R.PROJECT != null ?
      <a className='btn' href={`${globalThis.main.config.rootUrl}/projects/${R.PROJECT.id}`}>
        <span className='icon'><i className='fas fa-arrow-up-right-from-square'></i></span>
        <span className='text'>{this.translate('Go to project')}</span>
      </a>
      :
      <a className='btn' onClick={() => this.confirmConvertToProject(R.id)}>
        <span className='icon'><i className='fas fa-rotate-right'></i></span>
        <span className='text'>Convert to project</span>
      </a>
    );
  }
}

