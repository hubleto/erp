import React, { Component } from 'react'
import TableExtended, { TableExtendedProps, TableExtendedState } from '@hubleto/react-ui/ext/TableExtended';
import { ProgressBar } from 'primereact/progressbar';
import request from "@hubleto/react-ui/core/Request";
import Markdown from 'marked-react';

interface MessagesProps extends TableExtendedProps {
  idDiscussion?: number,
}

interface MessagesState extends TableExtendedState {
}

export default class Messages extends TableExtended<MessagesProps, MessagesState> {
  static defaultProps = {
    ...TableExtended.defaultProps,
    formUseModalSimple: true,
    model: 'Hubleto/App/Community/Discussions/Models/Message',
  }

  props: MessagesProps;
  state: MessagesState;

  translationContext: string = 'Hubleto\\App\\Community\\Discussions\\Loader';
  translationContextInner: string = 'Components\\Messages';

  refMessageTextarea: any;

  constructor(props: MessagesProps) {
    super(props);
    this.refMessageTextarea = React.createRef();
    this.state = this.getStateFromProps(props);
  }

  getStateFromProps(props: MessagesProps) {
    return {
      ...super.getStateFromProps(props),
    }
  }

  getFormModalProps(): any {
    let params = super.getFormModalProps();
    params.type = 'right wide';
    return params;
  }

  getEndpointParams(): any {
    return {
      ...super.getEndpointParams(),
      idDiscussion: this.props.idDiscussion,
    }
  }

  render() {
    if (!this.state.data) {
      return <ProgressBar mode="indeterminate" style={{ height: '8px' }}></ProgressBar>;
    }

    return <>
      <div className="list">
        {Object.keys(this.state.data.data ?? {}).map((index: any) => {
          const message = this.state.data.data[index];
          return <div className="btn btn-list-item btn-transparent gap-2 p-2 justify-between  " key={index}>
            <div>
              <div>
                {message.id_from == globalThis.hubleto.idUser || !message.FROM?.nick
                  ? <span className="badge badge-blue">{this.translate('you')}</span>
                  : <span className="badge">{message.FROM?.nick}</span>
                }
                <small className="ml-2">{message.sent}</small><br/>
              </div>
              <div><Markdown>{message.message}</Markdown></div>
            </div>
          </div>;
        })}
      </div>
      <div className="flex gap-2 items-end">
        <div className="grow">
          <textarea
            id={this.props.uid + '_message'}
            className="w-full h-24 shadow mt-2 border-gray-200"
            ref={this.refMessageTextarea}
            placeholder='Type your message here...'
          />
        </div>
        <div className="shrink">
          <button className="btn btn-large mb-2"
            onClick={() => {
              request.post(
                'discussions/api/send-message',
                {
                  idDiscussion: this.props.idDiscussion,
                  message: this.refMessageTextarea.current.value,
                },
                {},
                (res: any) => {
                  let data = this.state.data;
                  data.data.push(res.sentMessage);
                  this.setState({data: data}, () => {
                    this.refMessageTextarea.current.value = '';
                  });
                }
              );
            }}
          >
            <span className="icon"><i className="fas fa-paper-plane"></i></span>
          </button>
        </div>
      </div>
      Tip: You can use **markdown** syntax.
    </>
  }
}