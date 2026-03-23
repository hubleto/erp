import React from 'react';
import request from '@hubleto/react-ui/core/Request';
import TranslatedComponent from '@hubleto/react-ui/core/TranslatedComponent';

export interface ChatUiProps {
  [key: string]: any;
}

export interface ChatUiState {
  messages: Array<{ role: 'user' | 'model', content: string, html?: string }>;
  context: any,
  showContextDetails: boolean,
  inputValue: string;
  isLoading: boolean;
  promptMode: 'user' | 'developer';
}

export default class ChatUi extends TranslatedComponent<ChatUiProps, ChatUiState> {
  translationContext: string = 'Hubleto\\App\\Community\\AiAssistent\\Loader';
  translationContextInner: string = 'Components\\ChatUi';

  props: ChatUiProps;
  state: ChatUiState;

  constructor(props: ChatUiProps) {
    super(props);
    this.state = {
      messages: [],
      context: null,
      showContextDetails: false,
      inputValue: '',
      isLoading: false,
      promptMode: 'user'
    };
  }

  handleInputChange = (e: React.ChangeEvent<HTMLTextAreaElement>) => {
    this.setState({ inputValue: e.target.value });
  };

  handleKeyDown = (e: React.KeyboardEvent<HTMLTextAreaElement>) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      this.sendMessage();
    }
  };

  sendMessage = () => {
    const message = this.state.inputValue.trim();
    if (!message || this.state.isLoading) return;

    const searchParams = new URLSearchParams(window.location.search);
    const modelParam = searchParams.get('model');
    const idParam = searchParams.get('id');

    const newMessages: Array<{ role: 'user' | 'model', content: string, html?: string }> = [
      ...this.state.messages,
      { role: 'user', content: message }
    ];

    this.setState({
      messages: newMessages,
      inputValue: '',
      isLoading: true
    });

    request.post(
      'api/ai-assistant/chat',
      { message: message, messages: this.state.messages, mode: this.state.promptMode, model: modelParam, id: idParam },
      {},
      (response: any) => {
        if (response && response.status === 'success') {
          this.setState({
            messages: [...newMessages, { role: 'model', content: response.response, html: response.responseHtml }],
            context: response.context,
            isLoading: false
          });
        } else {
          this.setState({
            messages: [...newMessages, { role: 'model', content: response?.message || this.translate('Error occurred.') }],
            context: response.context,
            isLoading: false
          });
        }
      },
      (error: any) => {
        this.setState({
          messages: [...newMessages, { role: 'model', content: this.translate('Connection error.') }],
          isLoading: false
        });
      }
    );
  };

  insertSampleQuestion = (question: string) => {
    this.setState({ inputValue: question });
  };

  render() {
    const searchParams = new URLSearchParams(window.location.search);
    const modelParam = searchParams.get('model');
    const idParam = searchParams.get('id');
    const contextSource = modelParam ? modelParam.split('/').pop() : '';

    const sampleQuestions = [
      this.translate("What is the difference between a Lead and a Deal in Hubleto?"),
      this.translate("How can I create a new model and migration in Hubleto?"),
      this.translate("How do I set up calendar synchronization?"),
      this.translate("How do I create a new custom app?"),
      this.translate("How does the link between an order and an invoice work?")
    ];

    return (
      <div className="flex h-full bg-white dark:bg-slate-900 shadow-sm border border-slate-200 dark:border-slate-700">
        
        <div className="w-1/4 border-r border-slate-200 dark:border-slate-700 p-4 bg-slate-50 dark:bg-slate-800 flex flex-col gap-4 overflow-y-auto">
          <div className="flex bg-slate-200 dark:bg-slate-700 p-1 rounded-lg">
            <button
              onClick={() => this.setState({ promptMode: 'user' })}
              className={`flex-1 py-2 px-3 text-sm font-medium rounded-md transition-colors ${
                this.state.promptMode === 'user' 
                  ? 'bg-white dark:bg-slate-600 text-blue-600 dark:text-blue-400 shadow-sm' 
                  : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'
              }`}
            >
              <i className="fas fa-user mr-2"></i> {this.translate('User Mode')}
            </button>
            <button
              onClick={() => this.setState({ promptMode: 'developer' })}
              className={`flex-1 py-2 px-3 text-sm font-medium rounded-md transition-colors ${
                this.state.promptMode === 'developer' 
                  ? 'bg-white dark:bg-slate-600 text-blue-600 dark:text-blue-400 shadow-sm' 
                  : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white'
              }`}
            >
              <i className="fas fa-code mr-2"></i> {this.translate('Developer Mode')}
            </button>
          </div>
          
          {contextSource && <>
            <div className="mt-2 text-sm bg-blue-50 dark:bg-slate-700/50 border border-blue-200 dark:border-slate-600 rounded-md p-3 text-slate-700 dark:text-slate-300 flex flex-col items-start shadow-sm">
              <div className='flex items-center'>
                <i className="fas fa-database text-blue-500 dark:text-blue-400 mr-2"></i>
                <span>
                  <strong>{this.translate('Context')}:</strong> {contextSource} {idParam ? `#${idParam}` : ''}<br/>
                  {this.state.context ?
                    <button className='btn btn-transparent' onClick={() => { this.setState({showContextDetails: true }); }}>
                      <span className='text'>{this.translate('Show context details')}</span>
                    </button>
                  : null}
                </span>
              </div>
              {this.state.showContextDetails ? <pre className='text-xs w-full overflow-x-auto'>
                {JSON.stringify(this.state.context, null, 2)}
              </pre> : null}
            </div>
          </>}

          <h3 className="font-semibold text-lg text-slate-800 dark:text-slate-200 mt-2">{this.translate('Sample Questions')}</h3>
          <ul className="flex flex-col gap-2">
            {sampleQuestions.map((q, idx) => (
              <li key={idx}>
                <button 
                  className="text-left w-full text-sm text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-700 p-3 rounded-md border border-slate-200 dark:border-slate-600 hover:border-blue-400 hover:text-blue-600 dark:hover:border-blue-500 dark:hover:text-blue-400 transition-colors"
                  onClick={() => this.insertSampleQuestion(q)}
                >
                  {q}
                </button>
              </li>
            ))}
          </ul>
        </div>

        <div className="flex-1 flex flex-col bg-slate-50 dark:bg-slate-900 relative">
          
          <div className="flex-1 p-6 overflow-y-auto flex flex-col gap-4">
            {this.state.messages.length === 0 ? (
              <div className="flex-1 flex flex-col items-center justify-center text-slate-400 dark:text-slate-500">
                <img src={this.props.assetsUrl + "/images/hubi.png"} className="h-24 w-auto object-contain opacity-50 mb-2 grayscale" alt="Hubi" />
                <p>{this.translate('Hi, I am Hubi, your AIAssistant. How can I help you today with Hubleto ERP?')}</p>
              </div>
            ) : (
              this.state.messages.map((msg, idx) => (
                <div key={idx} className={`flex ${msg.role === 'user' ? 'justify-end' : 'justify-start'}`}>
                  <div 
                    className={`max-w-[90%] md:max-w-[85%] p-5 sm:p-6 shadow-md ${
                      msg.role === 'user' 
                        ? 'bg-blue-600 text-white rounded-2xl rounded-br-none' 
                        : 'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 rounded-2xl rounded-bl-none border border-slate-200 dark:border-slate-700'
                    }`}
                  >
                    {msg.html ? (
                      <div dangerouslySetInnerHTML={{ __html: msg.html }} className="prose prose-slate dark:prose-invert max-w-none text-base leading-relaxed prose-pre:bg-slate-800 prose-pre:text-slate-50" />
                    ) : (
                      <div className="whitespace-pre-wrap text-base leading-relaxed">{msg.content}</div>
                    )}
                  </div>
                </div>
              ))
            )}
            
            {this.state.isLoading && (
              <div className="flex justify-start">
                <div className="bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 p-4 rounded-xl rounded-bl-none shadow-sm border border-slate-200 dark:border-slate-700 flex items-center gap-2">
                  <i className="fas fa-spinner fa-spin"></i> {this.translate('Thinking...')}
                </div>
              </div>
            )}
          </div>

          <div className="p-4 border-t border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
            <div className="relative flex items-end gap-2">
              <textarea
                value={this.state.inputValue}
                onChange={this.handleInputChange}
                onKeyDown={this.handleKeyDown}
                placeholder={this.translate('Type your question here...')}
                className="flex-1 h-[50px] border border-slate-300 dark:border-slate-600 rounded-lg py-3 px-4 resize-none focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 dark:bg-slate-700 dark:text-slate-200"
                rows={1}
                disabled={this.state.isLoading}
              ></textarea>
              <button 
                onClick={this.sendMessage}
                disabled={this.state.isLoading || !this.state.inputValue.trim()}
                className="h-[50px] w-[50px] bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-slate-300 dark:disabled:bg-slate-600 disabled:cursor-not-allowed transition-colors flex items-center justify-center"
              >
                <i className="fas fa-paper-plane"></i>
              </button>
            </div>
            <div className="text-xs text-slate-400 dark:text-slate-500 mt-2 text-center">
              {this.translate('AI can make mistakes. Please verify answers in the official documentation.')}
            </div>
          </div>

        </div>
      </div>
    );
  }
}