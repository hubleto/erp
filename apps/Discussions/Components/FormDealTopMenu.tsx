import React, { Component, createRef } from 'react';
import FormDeal, { FormDealProps, FormDealState } from '@hubleto/apps/Deals/Components/FormDeal'
import TranslatedComponent from "@hubleto/react-ui/core/TranslatedComponent";
import request from '@hubleto/react-ui/core/Request';
import TableDiscussions from './TableDiscussions';
import ModalSimple from "@hubleto/react-ui/core/ModalSimple";
import DealFormActivity from '../../Deals/Components/DealFormActivity';

interface P {
  form: FormDeal<FormDealProps, FormDealState>
}

interface S {
  showDiscussions: boolean;
  showIdDiscussion: number,
}

export default class FormDealTopMenu extends TranslatedComponent<P, S> {
  props: P;
  state: S;

  translationContext: string = 'HubletoApp\\Community\\Discussions\\Loader::Components\\FormDiscussion';

  refTableDiscussions: any;

  constructor(props: P) {
    super(props);
    this.refTableDiscussions = createRef();
    this.state = { showDiscussions: false, showIdDiscussion: 0 };
  }

  addDiscussion(idDeal: number, topic: string) {
    request.get(
      'discussions/api/create-discussion',
      {
        externalModel: 'HubletoApp\\Community\\Deals\\Models\\Deal',
        externalId: idDeal,
        topic: topic,
      },
      (data: any) => {
        if (data.status == "success") {
          console.log(this.refTableDiscussions, this.refTableDiscussions.current);
          this.refTableDiscussions.current.reload();
        }
      }
    );
  }

  render() {
    const form = this.props.form;
    const R = form.state.record;

    if (R.id > 0) {
      return <>
        <button
          className="btn btn-transparent"
          onClick={() => { this.setState({showDiscussions: !this.state.showDiscussions}); }}
        >
          <span className="icon"><i className="fas fa-handshake"></i></span>
          <span className="text">{this.translate('Discussions')}</span>
        </button>
        {this.state.showDiscussions ? <>
          <ModalSimple
            uid='deals_table_discussions_modal'
            isOpen={true}
            type='centered'
            showHeader={true}
            title={<>
              <h2>Discussions</h2>
              <small>Deal: {R.title ? R.title : '-'}</small>
            </>}
            topMenu={
              <a className='btn btn-transparent' onClick={() => this.addDiscussion(R.id, R.title)}>
                <span className='icon'><i className='fas fa-rotate-right'></i></span>
                <span className='text'>Add discussion</span>
              </a>
            }
            onClose={(modal: ModalSimple) => { this.setState({showDiscussions: false}); }}
          >
            <TableDiscussions
              ref={this.refTableDiscussions}
              uid={form.props.uid + "_table_discussions"}
              tag={"DealDiscussions"}
              parentForm={form}
              externalModel='HubletoApp\\Community\\Deals\\Models\\Deal'
              externalId={R.id}
              recordId={this.state.showIdDiscussion}
              descriptionSource='both'
              description={{permissions: {canCreate: false}}}
            />
          </ModalSimple>
        </> : null}
      </>
    }
  }
}

