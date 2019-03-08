import numpy as np

def sigmoid(x):
    return 1.0/(1+ np.exp(-x))

def sigmoid_derivative(x):
    return x * (1.0 - x)

class NeuralNetwork:
    def __init__(self, x, y):
        self.input            = x
        self.input_weights    = np.random.rand(5, 10)
        self.hidden_weights   = np.random.rand(10, 5)
        self.output_weights   = np.random.rand(5, 3)
        self.y                = np.reshape(y, (1, 3))
        self.output           = np.zeros(self.y.shape)

    def feedforward(self):
        self.input_y  = sigmoid(np.dot(self.input, self.input_weights))
        self.hidden_y = sigmoid(np.dot(self.input_y, self.hidden_weights))
        print(self.hidden_y)
        self.output   = sigmoid(np.sum(np.dot(self.hidden_y, self.output_weights),axis=0))

    def backprop(self):
        # application of the chain rule to find derivative of the loss function with respect to weights2 and weights1
        d_output_weights = np.sum(np.dot(self.hidden_y, (2*(self.y - self.output) * sigmoid_derivative(self.output))), axis = 0)
        d_hidden_weights = np.dot(self.input_y.T, (2*np.dot(d_output_weights, self.output_weights.T) 
                                  * sigmoid_derivative(self.hidden_y)))
        d_input_weights  = np.dot(self.input.T, (2*np.dot(d_hidden_weights, self.hidden_weights.T) * sigmoid_derivative(self.input_y)))

        # update the weights with the derivative (slope) of the loss function
        self.input_weights  += d_input_weights
        self.hidden_weights += d_hidden_weights
        self.output_weights += d_output_weights

if __name__ == "__main__":
    X = np.random.rand(10, 5)
    y = np.array([[0],[0],[1]])
    nn = NeuralNetwork(X,y)

    for i in range(1500):
        nn.feedforward()
        nn.backprop()

    print(nn.output)
